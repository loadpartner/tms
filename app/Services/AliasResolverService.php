<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AliasResolverService
{
    /**
     * List of models that support aliases
     */
    protected array $aliasModels = [
        \App\Models\Shipments\Shipment::class,
        \App\Models\Shipments\ShipmentStop::class,
        \App\Models\Contact::class,
        \App\Models\Carriers\Carrier::class,
        \App\Models\Customers\Customer::class,
        \App\Models\Facility::class
    ];

    /**
     * Get a value using an alias
     * 
     * @param int|array $id The ID of the model
     * @param string|array $alias The alias in format "aliasName.property"
     * @return mixed|null The resolved value or null if not found
     */
    public function get($id, string|array $alias)
    {
        if (is_array($alias)) {
            $values = [];
            foreach ($alias as $a) {
                $values[$a] = $this->get($id, $a);
            }
            return $values;
        }

        [$modelAlias, $propertyAlias] = $this->parseAlias($alias);
        $model = $this->resolveModel($modelAlias, $id);
        if (!$model) {
            return null;
        }

        $result = $this->resolveAliasProperty($model, $propertyAlias);
        
        return $result;
    }

    /**
     * Get the alias name for a model
     * 
     * @param string $modelClass The model class
     * @return string The alias name
     */
    public function getModelAlias(string $modelClass) : string
    {
        return (new $modelClass)->getAliasName();
    }

    /**
     * Get the model class for an alias name
     * 
     * @param string $modelAlias The alias name
     * @return string The model class or empty string if not found
     */
    public function getModelClass(string $modelAlias): string
    {
        $models = $this->aliasModels;
        $modelAliasesMap = Cache::rememberForever("alias.models", function () use ($models) {
            $map = [];
            foreach ($models as $model) {
                $map[$this->getModelAlias($model)] = $model;
            }
            return $map;
        });
        return $modelAliasesMap[$modelAlias] ?? "";
    }



    /**
     * Parse an alias string into its components
     * 
     * @param string $alias The alias string (e.g., "shipment.number")
     * @return array The parsed components [modelAlias, property]
     */
    protected function parseAlias(string $alias): array
    {
        $parts = explode('.', $alias, 2);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException("Invalid alias format: {$alias}. Expected format: 'aliasName.property'");
        }
        
        return $parts;
    }

    /**
     * Resolve a model instance based on the alias name and ID
     * 
     * @param string $modelAlias The alias name for the model
     * @param int|array $id The ID of the model
     * @return Model|null The resolved model instance or null if not found
     */
    protected function resolveModel(string $modelAlias, $id): ?Model
    {
        $modelClass = $this->getModelClass($modelAlias);
        if (!$modelClass) {
            Log::warning("No model mapping found for alias: {$modelAlias}");
            return null;
        }

        $modelClass = $this->getModelClass($modelAlias);
        return $modelClass::find($id);
    }

    

    
    protected function resolveAliasProperty(Model $model, string $propertyAlias) : string
    {
        $property = $model->aliasProperties[$propertyAlias] ?? "";

        if ($property == "" && strpos($propertyAlias, '.') !== false) {
            list($leftAlias, $rightProperty) = explode('.', $propertyAlias, 2); // driver, name
            
            $nestedModel = $model->{$model->aliasProperties[$leftAlias] ?? null} ?? null;
            
            if ($nestedModel instanceof Model && in_array(\App\Traits\HasAliases::class, class_uses($nestedModel))) {
                return $this->resolveAliasProperty($nestedModel, $rightProperty);
            } else {
                throw new \Exception("Nested model {$leftAlias} is not a model or does not use HasAliases trait " . "ref id " . ($model->id ?? 'unknown') . " type " . get_class($model));
            }
        }
        
        if ($property == "") {
            // @phpstan-ignore-next-line
            throw new \Exception("Property {$propertyAlias} not found on " . $model->getAliasName() . " for model " . get_class($model) . " ref id " . ($model->id ?? 'unknown'));
        }
        
        $result = "";

        // if value is formatted as function:name then call it
        if (strpos($property, 'function:') === 0) {
            $function = substr($property, 9);
            if (method_exists($model, $function)) {
                $result = $model->$function();
            }
            else {
                // @phpstan-ignore-next-line
                throw new \Exception("Function {$function} does not exist on " . $model->getAliasName() . " ref id " . $model->id . " type " . get_class($model));
            }

        } else if ($property) {
            $result = $model->$property;
        }

        if ($result instanceof Model && in_array(\App\Traits\HasAliases::class, class_uses($result))) {
            $result = $this->resolveAliasProperty($result, $propertyAlias);
        }

        return $result;
    }
} 
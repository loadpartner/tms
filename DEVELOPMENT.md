# Development FAQ

This file provides some guidance on best practice, tips and tricks for developing against LoadPartner TMS

## Obtaining fresh testing data
The artisan command dev:refresh will clear your database and run the demo seeder to give you a test environment.

```bash
sail artisan dev:refresh
// or 
php artisan dev:refresh
```


The following test users will be created

ADMIN:

Email:  `admin@test.com`

Pass:   `password`

REGULAR:

Email:  `user@test.com`

Pass:   `password`


## Running pre-commit & other dev environment checks

This will run PHPStan and generate IDE completions for any actions.
``` bash
sail artisan dev:check
```

## Adding permissions
1. Add your new permission enum to `app/Enums/Permission.php`. Make sure you include an entry under `label()`!
2. Create a new database migration, call it whatever you want. In the migration, run `App\Enums\Permission::syncToDatabase();` for both up and down.
3. Run migrations to add your new permissions to the database!

## File uploads not working
Ensure you have storage linked by running
``` bash
sail artisan storage:link
```

## Zip to timezone lookups
We are using a backup sqlite database of US zip3s to timezone esimates. If you are trying to override the lookup you can define your own db connection under `config/database.php` or with the `ZIP3_TO_TIMEZONE_OVERRIDE_DB_DATABASE` env naming.

## Organization Defaults
The `CreateOrUpdateOrganizationDefaults` action is responsible for adding defaults to any new organizations upon creation. This actions is automatically run anytime an organization is created via the `created` hook on the model itself.

If you need to alter the default values of existing organizations, a migration is required.
If you want to just change defaults moving forward, you can modify the defaults action.
** NOTE ** When making changes to defaults, it's recommended to consider both a migration AND an update to the defaults action, so that both existing and new organizations will be correctly updated!


## Reflecting updates real-time between inertia and fetch (event bus)

### Inertia forms, reflecting prop updates
This is for components that are being filled from the inertia connection, data that is being provided at load, not async or self contained loads.

See ShipmentStopsList.tsx

Essentially need to use refs to avoid infinite loops.
```js
const { patch, setData, data, errors } = useForm<{
    stops: ShipmentStop[];
}>({
    stops: getSavedStops(),
});

const setDataRef = useRef(setData);

useEffect(() => {
    setDataRef.current({
        stops: getSavedStops(),
    });
}, [stops, getSavedStops]);
```

### Self contained data fetch/xhr components
Use the event bus to emit and subscribe to events which trigger reloads of the data
See Notes.tsx

#### Subscribing
```js
const { subscribe } = useEventBus();
const subscribeRef = useRef(subscribe);
const unsubscribeRef = useRef<(() => void) | null>(null);

useEffect(() => {
    refreshNotes();

    // Subscribe to the new event
    unsubscribeRef.current = subscribeRef.current(
        'note-changed-' + notableType + '-' + notableId,
        () => {
            refreshNotes();
        },
    );

    // Cleanup on unmount
    return () => {
        if (unsubscribeRef.current) {
            unsubscribeRef.current();
        }
    };
}, [notableType, notableId, refreshNotes]);
```

#### Emitting
```js
const { emit } = useEventBus();
emit('check-call-added-' + shipmentId);
```

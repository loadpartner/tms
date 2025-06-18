/*eslint-disable*/
/**
 * from https://github.com/MrLightful/shadcn-tree-view
 */

import { cn } from '@/lib/utils';
import * as AccordionPrimitive from '@radix-ui/react-accordion';
import { createDropdownMenuScope } from '@radix-ui/react-dropdown-menu';
import { cva } from 'class-variance-authority';
import { Check, ChevronRight, PencilIcon, X } from 'lucide-react';
import React, { useEffect, useRef, useState } from 'react';
import { Button } from './ui/button';
import { Input } from './ui/input';

const treeVariants = cva(
    'rounded group hover:before:opacity-100 before:absolute before:rounded-lg before:left-0 px-2 before:w-full before:opacity-0 before:bg-accent/70 before:h-[2rem] before:-z-10 hover:bg-primary/5',
);

const selectedTreeVariants = cva(
    'before:opacity-100 before:bg-accent/70 text-accent-foreground bg-primary/10',
);

const dragOverVariants = cva(
    'before:opacity-100 before:bg-primary/20 text-primary-foreground',
);

interface TreeDataItem {
    id: string;
    name: string;
    icon?: any;
    selectedIcon?: any;
    openIcon?: any;
    children?: TreeDataItem[];
    actions?: React.ReactNode;
    onClick?: () => void;
    draggable?: boolean;
    droppable?: boolean;
    className?: string;
    canEditName?: boolean;
    isEditingName?: boolean;
}

type TreeProps = React.HTMLAttributes<HTMLDivElement> & {
    data: TreeDataItem[] | TreeDataItem;
    initialSelectedItemId?: string;
    onSelectChange?: (item: TreeDataItem | undefined) => void;
    expandAll?: boolean;
    defaultNodeIcon?: any;
    defaultLeafIcon?: any;
    onDocumentDrag?: (sourceItem: TreeDataItem, targetItem: TreeDataItem) => void;
    onDocumentDragStart?: (sourceItem: TreeDataItem | undefined) => void;
};

type EditNameProps = (sourceItem: TreeDataItem, name: string) => void;

const TreeView = React.forwardRef<HTMLDivElement, TreeProps>(
    (
        {
            data,
            initialSelectedItemId,
            onSelectChange,
            expandAll,
            defaultLeafIcon,
            defaultNodeIcon,
            className,
            onDocumentDrag,
            onDocumentDragStart,
            ...props
        },
        ref,
    ) => {
        const [selectedItemId, setSelectedItemId] = React.useState<
            string | undefined
        >(initialSelectedItemId);
        
        const [draggedItem, setDraggedItem] = React.useState<TreeDataItem | null>(null);
        const [touchDragActive, setTouchDragActive] = React.useState(false);
        const [touchDragPosition, setTouchDragPosition] = React.useState({ x: 0, y: 0 });
        const touchTimeoutRef = React.useRef<NodeJS.Timeout | null>(null);

        const handleSelectChange = React.useCallback(
            (item: TreeDataItem | undefined) => {
                setSelectedItemId(item?.id);
                if (onSelectChange) {
                    onSelectChange(item);
                }
            },
            [onSelectChange],
        );

        const handleDragStart = React.useCallback((item: TreeDataItem) => {
            setDraggedItem(item);
            onDocumentDragStart?.(item);
        }, []);

        const handleDrop = React.useCallback((targetItem: TreeDataItem) => {
            if (draggedItem && onDocumentDrag && draggedItem.id !== targetItem.id) {
                onDocumentDrag(draggedItem, targetItem);
            }
            setDraggedItem(null);
            setTouchDragActive(false);
            onDocumentDragStart?.(undefined);
        }, [draggedItem, onDocumentDrag]);

        const handleTouchMove = React.useCallback((e: TouchEvent) => {
            if (touchDragActive && draggedItem) {
                e.preventDefault();
                setTouchDragPosition({
                    x: e.touches[0].clientX,
                    y: e.touches[0].clientY
                });
            }
        }, [touchDragActive, draggedItem]);

        // Add function to find the element under the touch position
        const findItemUnderTouch = React.useCallback((x: number, y: number): TreeDataItem | null => {
            // Get all tree nodes and leaves (items that could be drop targets)
            const elements = document.querySelectorAll('[data-tree-item-id]');

            // Find element under the point
            for (let i = 0; i < elements.length; i++) {
                const rect = elements[i].getBoundingClientRect();
                if (
                    x >= rect.left && 
                    x <= rect.right && 
                    y >= rect.top && 
                    y <= rect.bottom
                ) {

                    // Get the item id from data attribute
                    const itemId = elements[i].getAttribute('data-tree-item-id');

                    if (itemId) {
                        // Find the item in our data
                        const findItemById = (items: TreeDataItem[] | TreeDataItem): TreeDataItem | null => {
                            if (items instanceof Array) {
                                for (const item of items) {
                                    if (item.id === itemId) return item;
                                    if (item.children) {
                                        const found = findItemById(item.children);
                                        if (found) return found;
                                    }
                                }
                            } else {
                                if (items.id === itemId) return items;
                                if (items.children) {
                                    return findItemById(items.children);
                                }
                            }
                            return { id: itemId, name: itemId };
                        };
                        
                        return findItemById(data);
                    }
                    break;
                }
            }
            
            // If no tree item found, return the root container
            return { id: '', name: 'root' };
        }, [data]);

        React.useEffect(() => {
            if (touchDragActive) {
                document.addEventListener('touchmove', handleTouchMove, { passive: false });
                
                // Add handler for touchcancel as well
                const handleTouchCancel = () => {
                    setTouchDragActive(false);
                    setDraggedItem(null);
                    if (touchTimeoutRef.current) {
                        clearTimeout(touchTimeoutRef.current);
                        touchTimeoutRef.current = null;
                    }
                    onDocumentDragStart?.(undefined);
                };
                
                document.addEventListener('touchcancel', handleTouchCancel);
                
                const dragIndicator = document.createElement('div');
                dragIndicator.id = 'touch-drag-indicator';
                dragIndicator.innerText = draggedItem?.name || '';
                dragIndicator.style.position = 'fixed';
                dragIndicator.style.left = `${touchDragPosition.x}px`;
                dragIndicator.style.top = `${touchDragPosition.y}px`;
                dragIndicator.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                dragIndicator.style.color = 'white';
                dragIndicator.style.padding = '8px';
                dragIndicator.style.borderRadius = '4px';
                dragIndicator.style.pointerEvents = 'none';
                dragIndicator.style.zIndex = '9999';
                dragIndicator.style.transform = 'translate(-50%, -50%)';
                document.body.appendChild(dragIndicator);
                
                return () => {
                    document.removeEventListener('touchmove', handleTouchMove);
                    document.removeEventListener('touchcancel', handleTouchCancel);
                    const indicator = document.getElementById('touch-drag-indicator');
                    if (indicator) {
                        document.body.removeChild(indicator);
                    }
                    if (touchTimeoutRef.current) {
                        clearTimeout(touchTimeoutRef.current);
                    }
                };
            }
        }, [touchDragActive, handleTouchMove, draggedItem, touchDragPosition, onDocumentDragStart]);

        const handleTouchStart = React.useCallback((item: TreeDataItem, e: React.TouchEvent) => {
            if (item.draggable) {
                if (touchTimeoutRef.current) {
                    clearTimeout(touchTimeoutRef.current);
                }
                
                // Add a touch move handler that clears the timer to prevent accidental drag during scrolling
                const touchStartY = e.touches[0].clientY;
                const clearTouchTimerOnScroll = (moveEvent: TouchEvent) => {
                    const touchMoveY = moveEvent.touches[0].clientY;
                    // If scrolled more than 10px, cancel the potential drag
                    if (Math.abs(touchMoveY - touchStartY) > 10) {
                        if (touchTimeoutRef.current) {
                            clearTimeout(touchTimeoutRef.current);
                            touchTimeoutRef.current = null;
                        }
                        document.removeEventListener('touchmove', clearTouchTimerOnScroll);
                    }
                };
                
                document.addEventListener('touchmove', clearTouchTimerOnScroll, { passive: true });
                
                // Set a timeout to detect long press (500ms)
                touchTimeoutRef.current = setTimeout(() => {
                    setDraggedItem(item);
                    setTouchDragActive(true);
                    setTouchDragPosition({
                        x: e.touches[0].clientX,
                        y: e.touches[0].clientY
                    });
                    document.removeEventListener('touchmove', clearTouchTimerOnScroll);
                    onDocumentDragStart?.(item);
                }, 500);
            }
        }, [onDocumentDragStart]);

        const handleTouchEnd = React.useCallback((e: React.TouchEvent, originalItem: TreeDataItem) => {
            // Clear the timeout to prevent drag activation after touch end
            if (touchTimeoutRef.current) {
                clearTimeout(touchTimeoutRef.current);
                touchTimeoutRef.current = null;
            }
            
            // If we're in drag mode, find the item under the touch position
            if (touchDragActive && draggedItem) {
                // Get the touch coordinates
                const x = e.changedTouches[0].clientX;
                const y = e.changedTouches[0].clientY;
                
                // Find the item under the touch
                const targetItem = findItemUnderTouch(x, y);
                
                if (targetItem) {
                    handleDrop(targetItem);
                } else {
                    handleDrop({id: '', name: 'root'});
                }
            }
        }, [touchDragActive, draggedItem, handleDrop, findItemUnderTouch]);

        const expandedItemIds = React.useMemo(() => {
            if (!initialSelectedItemId) {
                return [] as string[];
            }

            const ids: string[] = [];

            function walkTreeItems(
                items: TreeDataItem[] | TreeDataItem,
                targetId: string,
            ) {
                if (items instanceof Array) {
                    for (let i = 0; i < items.length; i++) {
                        ids.push(items[i]!.id);
                        if (walkTreeItems(items[i]!, targetId) && !expandAll) {
                            return true;
                        }
                        if (!expandAll) ids.pop();
                    }
                } else if (!expandAll && items.id === targetId) {
                    return true;
                } else if (items.children) {
                    return walkTreeItems(items.children, targetId);
                }
            }

            walkTreeItems(data, initialSelectedItemId);
            return ids;
        }, [data, expandAll, initialSelectedItemId]);

        return (
            <div 
                className={cn('relative overflow-hidden p-2', className)}
                >
                <TreeItem
                    data={data}
                    ref={ref}
                    selectedItemId={selectedItemId}
                    handleSelectChange={handleSelectChange}
                    expandedItemIds={expandedItemIds}
                    defaultLeafIcon={defaultLeafIcon}
                    defaultNodeIcon={defaultNodeIcon}
                    handleDragStart={handleDragStart}
                    handleDrop={handleDrop}
                    draggedItem={draggedItem}
                    handleTouchStart={handleTouchStart}
                    handleTouchEnd={handleTouchEnd}
                    touchDragActive={touchDragActive}
                    {...props}
                />
                <div
                    className='w-full h-[48px]'
                    data-tree-item-id="root_bottom"
                    onDrop={(e) => { handleDrop({id: '', name: 'parent_div'})}}
                    onTouchEnd={(e) => {
                        if (touchDragActive && draggedItem) {
                            // Use the findItemUnderTouch to get the actual target
                            const x = e.changedTouches[0].clientX;
                            const y = e.changedTouches[0].clientY;
                            const targetItem = findItemUnderTouch(x, y);
                            
                            if (targetItem) {
                                handleDrop(targetItem);
                            } else {
                                handleDrop({id: '', name: 'root_bottom'});
                            }
                        }
                    }}>

                </div>
            </div>
        );
    },
);
TreeView.displayName = 'TreeView';

type TreeItemProps = TreeProps & {
    selectedItemId?: string;
    handleSelectChange: (item: TreeDataItem | undefined) => void;
    expandedItemIds: string[];
    defaultNodeIcon?: any;
    defaultLeafIcon?: any;
    handleDragStart?: (item: TreeDataItem) => void;
    handleDrop?: (item: TreeDataItem) => void;
    draggedItem: TreeDataItem | null;
    handleTouchStart?: (item: TreeDataItem, e: React.TouchEvent) => void;
    handleTouchEnd?: (e: React.TouchEvent, item: TreeDataItem) => void;
    touchDragActive?: boolean;
};

const TreeItem = React.forwardRef<HTMLDivElement, TreeItemProps>(
    (
        {
            className,
            data,
            selectedItemId,
            handleSelectChange,
            expandedItemIds,
            defaultNodeIcon,
            defaultLeafIcon,
            handleDragStart,
            handleDrop,
            draggedItem,
            handleTouchStart,
            handleTouchEnd,
            touchDragActive,
            
            ...props
        },
        ref,
    ) => {
        if (!(data instanceof Array)) {
            data = [data];
        }
        return (
            <div ref={ref} role="tree" className={className} {...props} >
                <ul>
                    {data.map((item) => (
                        <li key={item.id}>
                            {item.children ? (
                                <TreeNode
                                    item={item}
                                    selectedItemId={selectedItemId}
                                    expandedItemIds={expandedItemIds}
                                    handleSelectChange={handleSelectChange}
                                    defaultNodeIcon={defaultNodeIcon}
                                    defaultLeafIcon={defaultLeafIcon}
                                    handleDragStart={handleDragStart}
                                    handleDrop={handleDrop}
                                    draggedItem={draggedItem}
                                    handleTouchStart={handleTouchStart}
                                    handleTouchEnd={handleTouchEnd}
                                    touchDragActive={touchDragActive}
                                />
                            ) : (
                                <TreeLeaf
                                    item={item}
                                    selectedItemId={selectedItemId}
                                    handleSelectChange={handleSelectChange}
                                    defaultLeafIcon={defaultLeafIcon}
                                    handleDragStart={handleDragStart}
                                    handleDrop={handleDrop}
                                    draggedItem={draggedItem}
                                    handleTouchStart={handleTouchStart}
                                    handleTouchEnd={handleTouchEnd}
                                    touchDragActive={touchDragActive}
                                />
                            )}
                        </li>
                    ))}
                </ul>
            </div>
        );
    },
);
TreeItem.displayName = 'TreeItem';

const TreeNode = ({
    item,
    handleSelectChange,
    expandedItemIds,
    selectedItemId,
    defaultNodeIcon,
    defaultLeafIcon,
    handleDragStart,
    handleDrop,
    draggedItem,
    handleTouchStart,
    handleTouchEnd,
    touchDragActive,
}: {
    item: TreeDataItem;
    handleSelectChange: (item: TreeDataItem | undefined) => void;
    expandedItemIds: string[];
    selectedItemId?: string;
    defaultNodeIcon?: any;
    defaultLeafIcon?: any;
    handleDragStart?: (item: TreeDataItem) => void;
    handleDrop?: (item: TreeDataItem) => void;
    draggedItem: TreeDataItem | null;
    handleTouchStart?: (item: TreeDataItem, e: React.TouchEvent) => void;
    handleTouchEnd?: (e: React.TouchEvent, item: TreeDataItem) => void;
    touchDragActive?: boolean;
}) => {
    const [value, setValue] = React.useState(
        expandedItemIds.includes(item.id) ? [item.id] : [],
    );
    const [isDragOver, setIsDragOver] = React.useState(false);

    const onDragStart = (e: React.DragEvent) => {
        if (!item.draggable) {
            e.preventDefault();
            return;
        }
        e.dataTransfer.setData('text/plain', item.id);
        handleDragStart?.(item);
    };

    const onDragOver = (e: React.DragEvent) => {
        if (item.droppable !== false && draggedItem && draggedItem.id !== item.id) {
            e.preventDefault();
            setIsDragOver(true);
        }
    };

    const onDragLeave = () => {
        setIsDragOver(false);
    };

    const onDrop = (e: React.DragEvent) => {
        e.preventDefault();
        setIsDragOver(false);
        handleDrop?.(item);
    };

    const onTouchStart = (e: React.TouchEvent) => {
        handleTouchStart?.(item, e);
    };

    const onTouchEnd = (e: React.TouchEvent) => {
        handleTouchEnd?.(e, item);
        if (touchDragActive && draggedItem && draggedItem.id !== item.id && item.droppable !== false) {
            setIsDragOver(true);
            setTimeout(() => setIsDragOver(false), 200);
        }
    };

    return (
        <AccordionPrimitive.Root
            type="multiple"
            value={value}
            onValueChange={(s) => setValue(s)}
        >
            <AccordionPrimitive.Item value={item.id}>
                <AccordionTrigger
                    className={cn(
                        treeVariants(),
                        selectedItemId === item.id && selectedTreeVariants(),
                        isDragOver && dragOverVariants(),
                        item.className,
                    )}
                    data-tree-item-id={item.id}
                    onClick={() => {
                        handleSelectChange(item);
                        item.onClick?.();
                    }}
                    draggable={!!item.draggable}
                    onDragStart={onDragStart}
                    onDragOver={onDragOver}
                    onDragLeave={onDragLeave}
                    onDrop={onDrop}
                    onTouchStart={onTouchStart}
                    onTouchEnd={onTouchEnd}
                >
                    <TreeIcon
                        item={item}
                        isSelected={selectedItemId === item.id}
                        isOpen={value.includes(item.id)}
                        default={defaultNodeIcon}
                    />
                    <ItemNameDisplay item={item} />
                    <TreeActions isSelected={selectedItemId === item.id}>
                        {item.actions}
                    </TreeActions>
                </AccordionTrigger>
                <AccordionContent className="ml-4 border-l pl-1" >
                    <TreeItem
                        data={item.children ? item.children : item}
                        selectedItemId={selectedItemId}
                        handleSelectChange={handleSelectChange}
                        expandedItemIds={expandedItemIds}
                        defaultLeafIcon={defaultLeafIcon}
                        defaultNodeIcon={defaultNodeIcon}
                        handleDragStart={handleDragStart}
                        handleDrop={handleDrop}
                        draggedItem={draggedItem}
                        handleTouchStart={handleTouchStart}
                        handleTouchEnd={handleTouchEnd}
                        touchDragActive={touchDragActive}
                    />
                </AccordionContent>
            </AccordionPrimitive.Item>
        </AccordionPrimitive.Root>
    );
};
const ItemNameDisplay: React.FC<{ 
    item: TreeDataItem
}> = ({ item }) => {
    return <span className="truncate text-sm">{item.name}</span>;
};

const TreeLeaf = React.forwardRef<
    HTMLDivElement,
    React.HTMLAttributes<HTMLDivElement> & {
        item: TreeDataItem;
        selectedItemId?: string;
        handleSelectChange: (item: TreeDataItem | undefined) => void;
        defaultLeafIcon?: any;
        handleDragStart?: (item: TreeDataItem) => void;
        handleDrop?: (item: TreeDataItem) => void;
        draggedItem: TreeDataItem | null;
        handleTouchStart?: (item: TreeDataItem, e: React.TouchEvent) => void;
        handleTouchEnd?: (e: React.TouchEvent, item: TreeDataItem) => void;
        touchDragActive?: boolean;
    }
>(
    (
        {
            className,
            item,
            selectedItemId,
            handleSelectChange,
            defaultLeafIcon,
            handleDragStart,
            handleDrop,
            draggedItem,
            handleTouchStart,
            handleTouchEnd,
            touchDragActive,
            ...props
        },
        ref,
    ) => {
        const [isDragOver, setIsDragOver] = React.useState(false);

        const onDragStart = (e: React.DragEvent) => {
            if (!item.draggable) {
                e.preventDefault();
                return;
            }
            e.dataTransfer.setData('text/plain', item.id);
            handleDragStart?.(item);
        };

        const onDragOver = (e: React.DragEvent) => {
            if (item.droppable !== false && draggedItem && draggedItem.id !== item.id) {
                e.preventDefault();
                setIsDragOver(true);
            }
        };

        const onDragLeave = () => {
            setIsDragOver(false);
        };

        const onDrop = (e: React.DragEvent) => {
            e.preventDefault();
            setIsDragOver(false);
            handleDrop?.(item);
        };

        const onTouchStart = (e: React.TouchEvent) => {
            handleTouchStart?.(item, e);
        };

        const onTouchEnd = (e: React.TouchEvent) => {
            handleTouchEnd?.(e, item);
            if (touchDragActive && draggedItem && draggedItem.id !== item.id && item.droppable !== false) {
                setIsDragOver(true);
                setTimeout(() => setIsDragOver(false), 200);
            }
        };

        return (
            <div
                ref={ref}
                className={cn(
                    'ml-5 flex cursor-pointer items-center py-2 text-left before:right-1',
                    treeVariants(),
                    className,
                    selectedItemId === item.id && selectedTreeVariants(),
                    isDragOver && dragOverVariants(),
                    item.className,
                )}
                data-tree-item-id={item.id}
                onClick={() => {
                    handleSelectChange(item);
                    item.onClick?.();
                }}
                draggable={!!item.draggable}
                onDragStart={onDragStart}
                onDragOver={onDragOver}
                onDragLeave={onDragLeave}
                onDrop={onDrop}
                onTouchStart={onTouchStart}
                onTouchEnd={onTouchEnd}
                {...props}
                title={item.name}
            >
                <TreeIcon
                    item={item}
                    isSelected={selectedItemId === item.id}
                    default={defaultLeafIcon}
                />
                <ItemNameDisplay item={item} />
                <TreeActions isSelected={selectedItemId === item.id}>
                    {item.actions}
                </TreeActions>
            </div>
        );
    },
);
TreeLeaf.displayName = 'TreeLeaf';

const AccordionTrigger = React.forwardRef<
    React.ElementRef<typeof AccordionPrimitive.Trigger>,
    React.ComponentPropsWithoutRef<typeof AccordionPrimitive.Trigger>
>(({ className, children, ...props }, ref) => (
    <AccordionPrimitive.Header>
        <AccordionPrimitive.Trigger
            ref={ref}
            className={cn(
                'flex w-full flex-1 items-center py-2 transition-all first:[&[data-state=open]>svg]:rotate-90',
                className,
            )}
            {...props}
        >
            <ChevronRight className="mr-1 h-4 w-4 shrink-0 text-accent-foreground/50 transition-transform duration-200" />
            {children}
        </AccordionPrimitive.Trigger>
    </AccordionPrimitive.Header>
));
AccordionTrigger.displayName = AccordionPrimitive.Trigger.displayName;

const AccordionContent = React.forwardRef<
    React.ElementRef<typeof AccordionPrimitive.Content>,
    React.ComponentPropsWithoutRef<typeof AccordionPrimitive.Content>
>(({ className, children, ...props }, ref) => (
    <AccordionPrimitive.Content
        ref={ref}
        className={cn(
            'data-[state=closed]:animate-accordion-up data-[state=open]:animate-accordion-down overflow-hidden text-sm transition-all',
            className,
        )}
        {...props}
    >
        <div className="pb-1 pt-0">{children}</div>
    </AccordionPrimitive.Content>
));
AccordionContent.displayName = AccordionPrimitive.Content.displayName;

const TreeIcon = ({
    item,
    isOpen,
    isSelected,
    default: defaultIcon,
}: {
    item: TreeDataItem;
    isOpen?: boolean;
    isSelected?: boolean;
    default?: any;
}) => {
    let Icon = defaultIcon;
    if (isSelected && item.selectedIcon) {
        Icon = item.selectedIcon;
    } else if (isOpen && item.openIcon) {
        Icon = item.openIcon;
    } else if (item.icon) {
        Icon = item.icon;
    }
    return Icon ? <Icon className="mr-2 h-4 w-4 shrink-0" /> : <></>;
};

const TreeActions = ({
    children,
    isSelected,
}: {
    children: React.ReactNode;
    isSelected: boolean;
}) => {
    return (
        <div
            className={cn(
                isSelected ? 'block' : 'hidden',
                'absolute right-3 group-hover:block',
            )}
        >
            {children}
        </div>
    );
};

export { TreeView, type TreeDataItem };

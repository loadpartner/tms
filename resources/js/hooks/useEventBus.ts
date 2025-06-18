// useEventBus.ts
import { Notable } from '@/types/enums';
import EventEmitter from 'eventemitter3';

// Create a singleton event emitter
const eventEmitter = new EventEmitter();

export function useEventBus() {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const emit = (event: string, ...args: any[]) =>
        eventEmitter.emit(event, ...args);

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const subscribe = (event: string, callback: (...args: any[]) => void) => {
        eventEmitter.on(event, callback);

        // Return cleanup function for useEffect
        return () => eventEmitter.off(event, callback);
    };

    const emitNoteChanged = (notableType: Notable, notableId: number) => {
        emit('note-changed-' + notableType + '-' + notableId);
    };

    return { emit, subscribe, emitNoteChanged };
}

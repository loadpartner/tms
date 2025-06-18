import { Document } from '@/types';
import { Download } from 'lucide-react';
import { Button } from './ui/button';
import { Dialog, DialogContent, DialogFooter, DialogTitle } from './ui/dialog';

interface DocumentPreviewDialogProps {
    document: Document | undefined;
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onDownload?: (document: Document) => void;
}

export default function DocumentPreviewDialog({
    document,
    open,
    onOpenChange,
    onDownload,
}: DocumentPreviewDialogProps) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="flex h-[80vh] max-w-4xl flex-col">
                <DialogTitle>{document?.name}</DialogTitle>
                <div className="flex-grow overflow-hidden">
                    <iframe
                        src={
                            document ? route('documents.show', document.id) : ''
                        }
                        className="h-full w-full border-0"
                        title={`Preview of ${document?.name}`}
                    />
                </div>
                <DialogFooter className="mt-4">
                    <Button
                        variant={'ghost'}
                        onClick={() => onOpenChange(false)}
                    >
                        Close
                    </Button>
                    {onDownload && document && (
                        <Button
                            variant={'default'}
                            onClick={() => onDownload(document)}
                        >
                            <Download className="mr-2 h-4 w-4" />
                            Download
                        </Button>
                    )}
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

import FileUpload, { FileUploadRef } from '@/Components/FileUpload';
import { TreeDataItem, TreeView } from '@/Components/tree-view';
import { Button } from '@/Components/ui/button';
import { Document, DocumentFolder } from '@/types';
import { Documentable } from '@/types/enums';
import { router, useForm } from '@inertiajs/react';
import {
    Download,
    Eye,
    File,
    FileArchive,
    FileAudio,
    FileCode,
    FileImage,
    FileJson,
    FileSpreadsheet,
    FileText,
    FileVideo,
    Folder,
    FolderOpen,
    PencilIcon,
    Trash,
} from 'lucide-react';
import { useRef, useState } from 'react';
import { Dialog, DialogContent, DialogFooter, DialogTitle } from '../ui/dialog';
import { Input } from '../ui/input';
import { Progress } from '../ui/progress';

interface DocumentsListProps {
    documents: Document[];
    documentableType: Documentable;
    documentableId: number;
    folders?: DocumentFolder[];
}

export default function DocumentsList({
    documents,
    documentableType,
    documentableId,
    folders,
}: DocumentsListProps) {
    const fileUploadRef = useRef<FileUploadRef>(null);

    let remainingDocuments = [...documents];

    const documentData: TreeDataItem[] =
        folders?.map((folder) => {
            const data = {
                id: `folder-${folder.id ?? folder.name}`,
                name: folder.name,
                icon: Folder,
                openIcon: FolderOpen,
                draggable: folder.id ? true : false,
                droppable: true,
            } as TreeDataItem;

            const folderDocs = remainingDocuments.filter(
                (doc) => doc.folder_name == folder.name,
            );
            data.children = folderDocs.map((doc) =>
                documentToTreeDataItem(doc, {
                    onEdit: () => handleEditName(doc),
                    onDownload: () => downloadDocument(doc),
                    onPreview: () => openPreviewDialog(`document-${doc.id}`),
                }),
            );

            // remove the docs we just put in the folder
            remainingDocuments = remainingDocuments.filter(
                (doc) => doc.folder_name != folder.name,
            );

            if (data.children.length == 0) {
                data.icon = FolderOpen;
                data.children = undefined;
            }

            return data;
        }) ?? [];

    documentData?.push(
        ...remainingDocuments.map((doc) =>
            documentToTreeDataItem(doc, {
                onEdit: () => handleEditName(doc),
                onDownload: () => downloadDocument(doc),
                onPreview: () => openPreviewDialog(`document-${doc.id}`),
            }),
        ),
    );

    const {
        data: fileUploadData,
        setData: setFileUploadData,
        post,
        progress,
    } = useForm<{
        file: File | null;
        folder_name: string | null;
        documentable_type: string;
        documentable_id: number;
    }>({
        file: null,
        folder_name: null,
        documentable_type: documentableType,
        documentable_id: documentableId,
    });

    const handleFileUpload = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post(route('documents.store'), {
            onSuccess: () => {
                setFileUploadData('file', null);
                // Reset the file upload component using the ref
                if (fileUploadRef.current) {
                    fileUploadRef.current.reset();
                }
            },
            preserveScroll: true,
        });
    };

    const [deleteDocument, setDeleteDocument] = useState<
        Document | undefined
    >();
    const [showDeleteFileDialog, setshowDeleteFileDialog] =
        useState<boolean>(false);

    // Preview dialog state
    const [previewDocument, setPreviewDocument] = useState<
        Document | undefined
    >();
    const [showPreviewDialog, setShowPreviewDialog] = useState<boolean>(false);

    // Document editing state
    const [editingDocument, setEditingDocument] = useState<
        Document | undefined
    >();
    const [showEditDialog, setShowEditDialog] = useState<boolean>(false);
    const [newDocumentName, setNewDocumentName] = useState<string>('');

    const editNameRef = useRef<HTMLInputElement>(null);

    const handleEditName = (document: Document) => {
        setEditingDocument(document);
        setNewDocumentName(document.name);
        setShowEditDialog(true);

        const lastDotIndex = document.name.lastIndexOf('.');

        // Bit hacky
        // we are forcing this to run after this finishes with render so that
        // we properly highlight the right field
        // we shouhld use an effect but i had bugs with that
        // so here we are
        setTimeout(() => {
            if (lastDotIndex > 0) {
                editNameRef?.current?.setSelectionRange(0, lastDotIndex);
            } else {
                editNameRef?.current?.select();
            }
        }, 0);
    };

    const handleSaveDocumentName = () => {
        if (editingDocument && newDocumentName.trim()) {
            // Find the corresponding tree item
            const treeItem = documentData.find(
                (item) =>
                    item.id === `document-${editingDocument.id}` ||
                    (item.children &&
                        item.children.some(
                            (child) =>
                                child.id === `document-${editingDocument.id}`,
                        )),
            );

            if (treeItem) {
                // Call updateDocumentName with the treeItem and new name
                updateDocumentName(editingDocument.id, newDocumentName);
            }

            // Close the dialog
            setShowEditDialog(false);
        }
    };

    const confirmDeleteFile = (sourceItem: TreeDataItem) => {
        const sourceId = sourceItem.id.replace(/^(document|folder)-/, '');
        const sourceDocument = documents.find(
            (doc) => doc.id.toString() == sourceId,
        );

        if (!sourceDocument) {
            console.error(
                'failed to find document for source item',
                sourceItem,
            );
            return;
        }

        setDeleteDocument(sourceDocument);
        setshowDeleteFileDialog(true);
    };

    const submitDeleteFile = () => {
        router.delete(route('documents.destroy', deleteDocument?.id), {
            preserveScroll: true,
            onSuccess: () => {
                setshowDeleteFileDialog(false);
            },
        });
    };

    const handleDragAndDrop = (
        sourceItem: TreeDataItem,
        targetItem: TreeDataItem,
    ) => {
        if (targetItem.id == 'trash') {
            confirmDeleteFile(sourceItem);
            return;
        }

        const targetType = targetItem.id.startsWith('document-')
            ? 'document'
            : 'folder';
        const targetId = targetItem.id.replace(/^(document|folder)-/, '');

        let targetFolder = '';
        switch (targetType) {
            case 'document':
                targetFolder =
                    documents.find((doc) => doc.id.toString() == targetId)
                        ?.folder_name ?? '';
                break;
            case 'folder':
                // TODO - this might break in the future if the name
                // gets changed for front end showing or something
                targetFolder = targetId;
                break;
            default:
                console.error('Failed to find a folder');
        }
        const sourceId = sourceItem.id.replace(/^(document|folder)-/, '');

        // Make sure data is properly set before sending the request
        const dataToSend = {
            folder_name: targetFolder,
        };

        router.put(route('documents.update', sourceId), dataToSend, {
            onError: (e) => {
                console.error('Error', e);
            },
            preserveScroll: true,
        });
    };

    const updateDocumentName = (sourceId: number, name: string) => {
        router.put(
            route('documents.update', sourceId),
            {
                file_name: name,
            },
            {
                onError: (e) => {
                    console.error('Error', e);
                },
                preserveScroll: true,
            },
        );
    };

    const downloadDocument = (document: Document) => {
        if (document) {
            // Create a download link for the document
            const downloadUrl = route('documents.show', document.id);

            // Create an invisible anchor element
            const link = window.document.createElement('a');
            link.href = downloadUrl;
            link.setAttribute('download', document.name);
            window.document.body.appendChild(link);

            // Trigger the download
            link.click();

            // Clean up - remove the element
            window.document.body.removeChild(link);
        } else {
            console.error('Document not found');
        }
    };

    const openPreviewDialog = (nodeId?: string) => {
        // If the nodeId is not a doc then return
        if (!nodeId?.startsWith('document-')) {
            return;
        }
        const sourceId = nodeId.replace(/^(document)-/, '');
        // Find the document with the matching ID
        const docToPreview = documents.find(
            (doc) => doc.id.toString() === sourceId,
        );

        if (docToPreview) {
            setPreviewDocument(docToPreview);
            setShowPreviewDialog(true);
        } else {
            console.error('Document not found with ID:', sourceId);
        }
    };

    const [activeDragItem, setActiveDragItem] = useState<TreeDataItem>();
    const handleDragAndDropStart = (sourceItem: TreeDataItem | undefined) => {
        setActiveDragItem(sourceItem);
    };

    return (
        <div>
            <div>
                <TreeView
                    data={documentData}
                    onDocumentDrag={handleDragAndDrop}
                    onDocumentDragStart={handleDragAndDropStart}
                />
                <div
                    className="flex w-fit gap-1 border-2 border-dashed p-2 text-sm text-muted-foreground"
                    onDrop={() => {
                        activeDragItem &&
                            handleDragAndDrop(activeDragItem, {
                                id: 'trash',
                                name: 'trash',
                            });
                    }}
                    data-tree-item-id={'trash'}
                >
                    <Trash className="inline h-4 w-4" />
                    <span className="">trashcan</span>
                </div>
            </div>
            <form
                onSubmit={handleFileUpload}
                className="mx-auto flex w-fit flex-col gap-2"
            >
                <FileUpload
                    ref={fileUploadRef}
                    onFileChange={(file) => setFileUploadData('file', file)}
                    initialPreview={fileUploadData.file?.name}
                />
                <Button
                    disabled={!fileUploadData.file || progress != null}
                    className="mx-auto w-fit"
                    type="submit"
                >
                    Upload
                </Button>
                {progress && <Progress value={progress.percentage} />}
            </form>

            {/* Delete Document Dialog */}
            <Dialog
                open={showDeleteFileDialog}
                onOpenChange={setshowDeleteFileDialog}
            >
                <DialogContent>
                    <DialogTitle>Delete Document?</DialogTitle>
                    <div>
                        <span className="p-1 font-bold">
                            {deleteDocument?.name}
                        </span>{' '}
                        will be permanently deleted
                    </div>
                    <DialogFooter>
                        <Button
                            variant={'ghost'}
                            onClick={() => {
                                setshowDeleteFileDialog(false);
                            }}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant={'destructive'}
                            onClick={submitDeleteFile}
                        >
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Preview Document Dialog */}
            <Dialog
                open={showPreviewDialog}
                onOpenChange={setShowPreviewDialog}
            >
                <DialogContent className="flex h-[80vh] max-w-4xl flex-col">
                    <DialogTitle>{previewDocument?.name}</DialogTitle>
                    <div className="flex-grow overflow-hidden">
                        <iframe
                            src={
                                previewDocument
                                    ? route(
                                          'documents.show',
                                          previewDocument.id,
                                      )
                                    : ''
                            }
                            className="h-full w-full border-0"
                            title={`Preview of ${previewDocument?.name}`}
                        />
                    </div>
                    <DialogFooter className="mt-4">
                        <Button
                            variant={'ghost'}
                            onClick={() => setShowPreviewDialog(false)}
                        >
                            Close
                        </Button>
                        <Button
                            variant={'default'}
                            onClick={() =>
                                previewDocument &&
                                downloadDocument(previewDocument)
                            }
                        >
                            <Download className="mr-2 h-4 w-4" />
                            Download
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Edit Document Name Dialog */}
            <Dialog open={showEditDialog} onOpenChange={setShowEditDialog}>
                <DialogContent>
                    <DialogTitle>Edit Document Name</DialogTitle>
                    <div className="py-4">
                        <Input
                            type="text"
                            value={newDocumentName}
                            onChange={(e) => setNewDocumentName(e.target.value)}
                            onKeyDown={(e) => {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    handleSaveDocumentName();
                                }
                            }}
                            ref={editNameRef}
                            className="w-full rounded-md border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                            autoFocus
                        />
                    </div>
                    <DialogFooter>
                        <Button
                            variant="ghost"
                            onClick={() => setShowEditDialog(false)}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="default"
                            onClick={handleSaveDocumentName}
                            disabled={!newDocumentName.trim()}
                        >
                            Save
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}

function getDocumentIcon(extension: string) {
    switch (extension) {
        // Document types
        case 'pdf':
        case 'doc':
        case 'docx':
        case 'txt':
        case 'rtf':
            return FileText;

        // Spreadsheet types
        case 'xls':
        case 'xlsx':
        case 'csv':
            return FileSpreadsheet;

        // Image types
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'svg':
        case 'webp':
            return FileImage;

        // Video types
        case 'mp4':
        case 'avi':
        case 'mov':
        case 'wmv':
        case 'webm':
            return FileVideo;

        // Audio types
        case 'mp3':
        case 'wav':
        case 'ogg':
        case 'flac':
            return FileAudio;

        // Code types
        case 'html':
        case 'css':
        case 'js':
        case 'ts':
        case 'jsx':
        case 'tsx':
        case 'php':
        case 'py':
            return FileCode;

        // Data types
        case 'json':
        case 'xml':
            return FileJson;

        // Archive types
        case 'zip':
        case 'rar':
        case 'tar':
        case 'gz':
            return FileArchive;

        // Default file icon for unknown types
        default:
            return File;
    }
}

interface DocumentActionHandlers {
    onEdit?: () => void;
    onDownload?: () => void;
    onPreview?: () => void;
}

function documentToTreeDataItem(
    doc: Document,
    handlers?: DocumentActionHandlers,
) {
    const extension = doc.name.split('.').pop()?.toLowerCase() || '';
    const icon = getDocumentIcon(extension);

    const actionButtons = (
        <div className="flex translate-x-0 transform items-center space-x-1 transition-all duration-300">
            <Button
                variant="outline"
                size="icon"
                className="h-6 w-6"
                onClick={(e) => {
                    e.stopPropagation();
                    handlers?.onEdit?.();
                }}
            >
                <PencilIcon className="h-4 w-4" />
            </Button>
            <Button
                variant="outline"
                size="icon"
                className="h-6 w-6"
                onClick={(e) => {
                    e.stopPropagation();
                    handlers?.onDownload?.();
                }}
            >
                <Download className="h-4 w-4" />
            </Button>
            <Button
                variant="outline"
                size="icon"
                className="h-6 w-6"
                onClick={(e) => {
                    e.stopPropagation();
                    handlers?.onPreview?.();
                }}
            >
                <Eye className="h-4 w-4" />
            </Button>
        </div>
    );

    return {
        id: `document-${doc.id}`,
        name: doc.name,
        icon: icon,
        canEditName: true,
        selectedIcon: undefined,
        draggable: true,
        droppable: false,
        actions: actionButtons,
    } as TreeDataItem;
}

import { useState, useRef } from 'react';
import { router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Card, CardContent } from '@/components/ui/card';
import { Upload, FileText, CheckCircle2, XCircle, Clock, AlertCircle, Trash2, Eye, Loader2, FileUp } from 'lucide-react';
import type { PageProps, PaginatedResponse } from '@/Types';

interface DocumentImport {
    id: number; document_type: string; status: string; file_name: string | null;
    file_type: string | null; file_size: number | null;
    records_imported: number | null; records_skipped: number | null;
    created_at: string; processed_at: string | null; imported_at: string | null;
    user?: { name: string };
}

interface Props {
    imports: PaginatedResponse<DocumentImport>;
    filters: { status?: string; document_type?: string };
    stats: { total: number; uploaded: number; processing: number; imported: number; failed: number };
}

const STATUS_STYLE: Record<string, string> = {
    uploaded: 'bg-blue-100 text-blue-700', processing: 'bg-amber-100 text-amber-700',
    extracted: 'bg-purple-100 text-purple-700', reviewed: 'bg-indigo-100 text-indigo-700',
    imported: 'bg-green-100 text-green-700', failed: 'bg-red-100 text-red-700',
};
const STATUS_ICON: Record<string, typeof Upload> = {
    uploaded: FileUp, processing: Loader2, extracted: Eye, reviewed: CheckCircle2, imported: CheckCircle2, failed: XCircle,
};
const DOC_TYPE_LABELS: Record<string, string> = {
    report_card: 'Report Card', admission_form: 'Admission Form', attendance_register: 'Attendance Register',
    fee_receipt: 'Fee Receipt', certificate: 'Certificate', staff_record: 'Staff Record',
};

export default function ImportsIndex({ imports, filters, stats }: Props) {
    const { flash } = usePage<PageProps>().props;
    const fileRef = useRef<HTMLInputElement>(null);
    const [uploading, setUploading] = useState(false);
    const [docType, setDocType] = useState('report_card');

    function applyFilter(key: string, value: string) {
        router.get('/school/imports', { ...filters, [key]: value || undefined }, { preserveScroll: true });
    }

    function handleUpload(e: React.FormEvent) {
        e.preventDefault();
        const file = fileRef.current?.files?.[0];
        if (!file) return;
        setUploading(true);
        const form = new FormData();
        form.append('file', file);
        form.append('document_type', docType);
        router.post('/school/imports/upload', form, {
            onFinish: () => { setUploading(false); if (fileRef.current) fileRef.current.value = ''; },
        });
    }

    function handleDelete(imp: DocumentImport) {
        if (confirm(`Delete this import record?`)) {
            router.delete(`/school/imports/${imp.id}`);
        }
    }

    function formatBytes(bytes: number | null) {
        if (!bytes) return '—';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    const statCards = [
        { label: 'Total', value: stats.total, color: 'text-indigo-600', icon: FileText },
        { label: 'Uploaded', value: stats.uploaded, color: 'text-blue-600', icon: Upload },
        { label: 'Processing', value: stats.processing, color: 'text-amber-600', icon: Loader2 },
        { label: 'Imported', value: stats.imported, color: 'text-green-600', icon: CheckCircle2 },
        { label: 'Failed', value: stats.failed, color: 'text-red-600', icon: XCircle },
    ];

    return (
        <AppLayout title="AI Document Import">
            <div className="space-y-6">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-bold text-slate-900 dark:text-white">AI Document Import</h1>
                        <p className="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Upload documents and let AI extract structured data</p>
                    </div>
                </div>

                {flash?.success && <div className="rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{flash.success}</div>}
                {flash?.error && <div className="rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{flash.error}</div>}

                {/* Stats */}
                <div className="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    {statCards.map(({ label, value, color, icon: Icon }) => (
                        <Card key={label} className="border-slate-200 dark:border-slate-800">
                            <CardContent className="p-3 flex items-center gap-2">
                                <Icon className={`w-4 h-4 ${color}`} />
                                <div>
                                    <p className="text-lg font-bold text-slate-900 dark:text-white">{value}</p>
                                    <p className="text-[10px] text-slate-500">{label}</p>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {/* Upload Area */}
                <Card className="border-slate-200 dark:border-slate-800">
                    <CardContent className="p-5">
                        <form onSubmit={handleUpload} className="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                            <div className="flex-1 space-y-1.5 w-full sm:w-auto">
                                <p className="text-sm font-medium text-slate-700 dark:text-slate-300">Upload Document</p>
                                <p className="text-xs text-slate-400">Supports JPG, PNG, PDF, WEBP — max 20MB</p>
                                <input ref={fileRef} type="file" accept=".jpg,.jpeg,.png,.pdf,.webp" className="block w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            </div>
                            <div className="space-y-1.5">
                                <p className="text-xs text-slate-500 font-medium">Document Type</p>
                                <Select value={docType} onValueChange={setDocType}>
                                    <SelectTrigger className="w-44"><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(DOC_TYPE_LABELS).map(([k, v]) => <SelectItem key={k} value={k}>{v}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                            </div>
                            <Button type="submit" disabled={uploading} className="bg-indigo-600 hover:bg-indigo-700 text-white inline-flex items-center gap-2">
                                {uploading ? <Loader2 className="w-4 h-4 animate-spin" /> : <Upload className="w-4 h-4" />}
                                {uploading ? 'Uploading...' : 'Upload'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                {/* Filters */}
                <div className="flex gap-3 flex-wrap">
                    <Select value={filters.status ?? ''} onValueChange={v => applyFilter('status', v)}>
                        <SelectTrigger className="w-36"><SelectValue placeholder="All Status" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">All Status</SelectItem>
                            {Object.keys(STATUS_STYLE).map(s => <SelectItem key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</SelectItem>)}
                        </SelectContent>
                    </Select>
                    <Select value={filters.document_type ?? ''} onValueChange={v => applyFilter('document_type', v)}>
                        <SelectTrigger className="w-40"><SelectValue placeholder="All Types" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">All Types</SelectItem>
                            {Object.entries(DOC_TYPE_LABELS).map(([k, v]) => <SelectItem key={k} value={k}>{v}</SelectItem>)}
                        </SelectContent>
                    </Select>
                </div>

                {/* Table */}
                <div className="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow className="bg-slate-50 dark:bg-slate-900">
                                <TableHead>File</TableHead>
                                <TableHead>Type</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="hidden md:table-cell">Size</TableHead>
                                <TableHead className="hidden md:table-cell">Imported By</TableHead>
                                <TableHead className="hidden md:table-cell">Created</TableHead>
                                <TableHead>Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {imports.data.length === 0 ? (
                                <TableRow><TableCell colSpan={7} className="text-center py-16 text-slate-400">No imports yet. Upload a document to get started.</TableCell></TableRow>
                            ) : imports.data.map(imp => {
                                const StatusIcon = STATUS_ICON[imp.status] ?? FileText;
                                return (
                                    <TableRow key={imp.id}>
                                        <TableCell>
                                            <p className="font-medium text-sm text-slate-900 dark:text-white truncate max-w-[200px]">{imp.file_name ?? 'Unknown'}</p>
                                        </TableCell>
                                        <TableCell className="text-xs text-slate-500">{DOC_TYPE_LABELS[imp.document_type] ?? imp.document_type}</TableCell>
                                        <TableCell>
                                            <Badge className={`border-0 text-xs inline-flex items-center gap-1 ${STATUS_STYLE[imp.status] ?? ''}`}>
                                                <StatusIcon className="w-3 h-3" /> {imp.status.charAt(0).toUpperCase() + imp.status.slice(1)}
                                            </Badge>
                                        </TableCell>
                                        <TableCell className="hidden md:table-cell text-xs text-slate-500">{formatBytes(imp.file_size)}</TableCell>
                                        <TableCell className="hidden md:table-cell text-xs text-slate-500">{imp.user?.name ?? '—'}</TableCell>
                                        <TableCell className="hidden md:table-cell text-xs text-slate-500">{new Date(imp.created_at).toLocaleDateString()}</TableCell>
                                        <TableCell>
                                            <div className="flex gap-1">
                                                <a href={`/school/imports/${imp.id}`}>
                                                    <Button variant="outline" size="sm" className="text-xs inline-flex items-center gap-1">
                                                        <Eye className="w-3 h-3" /> View
                                                    </Button>
                                                </a>
                                                <Button variant="ghost" size="icon" className="w-8 h-8 text-red-500" onClick={() => handleDelete(imp)}>
                                                    <Trash2 className="w-3 h-3" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                </div>

                {/* Pagination */}
                {imports.meta.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-xs text-slate-400">Page {imports.meta.current_page} of {imports.meta.last_page} ({imports.meta.total} total)</p>
                        <div className="flex gap-2">
                            {imports.links.prev && <Button variant="outline" size="sm" onClick={() => router.get(imports.links.prev!)}>Previous</Button>}
                            {imports.links.next && <Button variant="outline" size="sm" onClick={() => router.get(imports.links.next!)}>Next</Button>}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

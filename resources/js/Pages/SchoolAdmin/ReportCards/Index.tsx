import { useState } from 'react';
import { router, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { ConfirmDialog } from '@/components/ui/ConfirmDialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Card, CardContent } from '@/components/ui/card';
import { Plus, Send, Check, Upload, ArrowUpCircle, FileText, GraduationCap } from 'lucide-react';
import type { PageProps, PaginatedResponse } from '@/Types';

interface Student { id: number; first_name: string; last_name: string; admission_no: string; }
interface SchoolClass { id: number; name: string; }
interface AcademicTerm { id: number; name: string; }
interface AcademicYear { id: number; name: string; is_current: boolean; }

interface ReportCard {
    id: number; student_id: number; class_id: number; term_id: number; academic_year_id: number;
    total_marks: number; obtained_marks: number; percentage: number; grade: string; gpa: number;
    status: string; promotion_status: string | null;
    total_school_days: number; days_present: number; days_absent: number;
    created_at: string;
    student?: Student; schoolClass?: SchoolClass; term?: AcademicTerm; academicYear?: AcademicYear;
}

interface Props extends PageProps {
    reportCards: PaginatedResponse<ReportCard>;
    classes: SchoolClass[];
    terms: AcademicTerm[];
    academicYears: AcademicYear[];
    filters: { class_id?: string; term_id?: string; status?: string };
}

const STATUS_STYLE: Record<string, string> = {
    draft: 'bg-slate-100 text-slate-600',
    approved: 'bg-blue-100 text-blue-700',
    published: 'bg-green-100 text-green-700',
};

export default function ReportCardsIndex({ reportCards, classes, terms, academicYears, filters }: Props) {
    const [generateOpen, setGenerateOpen] = useState(false);
    const [bulkOpen, setBulkOpen] = useState(false);
    const [promoteOpen, setPromoteOpen] = useState(false);
    const [confirmAction, setConfirmAction] = useState<{ type: string; data: Record<string, string> } | null>(null);

    const genForm = useForm({ class_id: '', term_id: '', academic_year_id: '', section_id: '' });
    const bulkForm = useForm({ class_id: '', term_id: '' });
    const promoteForm = useForm({ class_id: '', term_id: '', academic_year_id: '', min_gpa: '2.0' });

    function applyFilter(key: string, value: string) {
        router.get('/school/report-cards', { ...filters, [key]: value || undefined }, { preserveScroll: true });
    }

    function handleApprove(id: number) {
        setConfirmAction({ type: 'approve', data: { id: String(id) } });
    }

    function handlePublish(id: number) {
        setConfirmAction({ type: 'publish', data: { id: String(id) } });
    }

    function executeConfirm() {
        if (!confirmAction) return;
        if (confirmAction.type === 'approve') {
            router.patch(`/school/report-cards/${confirmAction.data.id}/approve`, {}, { onFinish: () => setConfirmAction(null) });
        } else if (confirmAction.type === 'publish') {
            router.patch(`/school/report-cards/${confirmAction.data.id}/publish`, {}, { onFinish: () => setConfirmAction(null) });
        } else if (confirmAction.type === 'bulk-publish') {
            bulkForm.post('/school/report-cards/bulk-publish', { onSuccess: () => { setBulkOpen(false); setConfirmAction(null); } });
        } else if (confirmAction.type === 'promote') {
            promoteForm.post('/school/report-cards/promote-students', { onSuccess: () => { setPromoteOpen(false); setConfirmAction(null); } });
        }
    }

    const publishedCount = reportCards.data.filter((r) => r.status === 'published').length;
    const approvedCount = reportCards.data.filter((r) => r.status === 'approved').length;
    const avgGpa = reportCards.data.length > 0 ? (reportCards.data.reduce((s, r) => s + (r.gpa ?? 0), 0) / reportCards.data.length).toFixed(2) : '—';

    return (
        <AppLayout breadcrumbs={[{ label: 'Report Cards' }]}>
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-slate-900 dark:text-white">Report Cards</h1>
                        <p className="text-sm text-slate-500">Generate, approve, publish & promote students</p>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" size="sm" onClick={() => setBulkOpen(true)}><Send className="w-4 h-4 mr-1" /> Bulk Publish</Button>
                        <Button variant="outline" size="sm" onClick={() => setPromoteOpen(true)}><ArrowUpCircle className="w-4 h-4 mr-1" /> Promote Students</Button>
                        <Button size="sm" className="bg-indigo-600 hover:bg-indigo-700 text-white" onClick={() => setGenerateOpen(true)}><Plus className="w-4 h-4 mr-1" /> Generate Reports</Button>
                    </div>
                </div>

                <div className="grid grid-cols-4 gap-4">
                    <Card className="dark:bg-slate-900 border-slate-200 dark:border-slate-800">
                        <CardContent className="flex items-center gap-3 p-4">
                            <FileText className="w-8 h-8 text-indigo-600" />
                            <div><p className="text-2xl font-bold">{reportCards.meta.total}</p><p className="text-xs text-slate-500">Total Reports</p></div>
                        </CardContent>
                    </Card>
                    <Card className="dark:bg-slate-900 border-slate-200 dark:border-slate-800">
                        <CardContent className="flex items-center gap-3 p-4">
                            <Check className="w-8 h-8 text-blue-600" />
                            <div><p className="text-2xl font-bold">{approvedCount}</p><p className="text-xs text-slate-500">Approved</p></div>
                        </CardContent>
                    </Card>
                    <Card className="dark:bg-slate-900 border-slate-200 dark:border-slate-800">
                        <CardContent className="flex items-center gap-3 p-4">
                            <Send className="w-8 h-8 text-green-600" />
                            <div><p className="text-2xl font-bold">{publishedCount}</p><p className="text-xs text-slate-500">Published</p></div>
                        </CardContent>
                    </Card>
                    <Card className="dark:bg-slate-900 border-slate-200 dark:border-slate-800">
                        <CardContent className="flex items-center gap-3 p-4">
                            <GraduationCap className="w-8 h-8 text-purple-600" />
                            <div><p className="text-2xl font-bold">{avgGpa}</p><p className="text-xs text-slate-500">Avg GPA</p></div>
                        </CardContent>
                    </Card>
                </div>

                <div className="flex gap-3">
                    <Select value={filters.class_id ?? 'all'} onValueChange={(v) => applyFilter('class_id', v === 'all' ? '' : v)}>
                        <SelectTrigger className="w-44 h-9"><SelectValue placeholder="All Classes" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Classes</SelectItem>
                            {classes.map((c) => <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>)}
                        </SelectContent>
                    </Select>
                    <Select value={filters.term_id ?? 'all'} onValueChange={(v) => applyFilter('term_id', v === 'all' ? '' : v)}>
                        <SelectTrigger className="w-44 h-9"><SelectValue placeholder="All Terms" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Terms</SelectItem>
                            {terms.map((t) => <SelectItem key={t.id} value={String(t.id)}>{t.name}</SelectItem>)}
                        </SelectContent>
                    </Select>
                    <Select value={filters.status ?? 'all'} onValueChange={(v) => applyFilter('status', v === 'all' ? '' : v)}>
                        <SelectTrigger className="w-40 h-9"><SelectValue placeholder="All Status" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Status</SelectItem>
                            {['draft', 'approved', 'published'].map((s) => <SelectItem key={s} value={s}>{s}</SelectItem>)}
                        </SelectContent>
                    </Select>
                </div>

                <div className="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Student</TableHead>
                                <TableHead>Class</TableHead>
                                <TableHead>Term</TableHead>
                                <TableHead>Score</TableHead>
                                <TableHead>GPA</TableHead>
                                <TableHead>Grade</TableHead>
                                <TableHead>Attendance</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {reportCards.data.length === 0 ? (
                                <TableRow><TableCell colSpan={9} className="text-center py-8 text-slate-400">No report cards found</TableCell></TableRow>
                            ) : reportCards.data.map((rc) => (
                                <TableRow key={rc.id}>
                                    <TableCell className="font-medium">{rc.student?.first_name} {rc.student?.last_name}</TableCell>
                                    <TableCell>{rc.schoolClass?.name}</TableCell>
                                    <TableCell>{rc.term?.name}</TableCell>
                                    <TableCell>{rc.percentage.toFixed(1)}%</TableCell>
                                    <TableCell>{rc.gpa?.toFixed(2) ?? '—'}</TableCell>
                                    <TableCell><Badge>{rc.grade}</Badge></TableCell>
                                    <TableCell>{rc.total_school_days > 0 ? `${rc.days_present}/${rc.total_school_days}` : '—'}</TableCell>
                                    <TableCell><Badge className={STATUS_STYLE[rc.status] ?? ''}>{rc.status}</Badge></TableCell>
                                    <TableCell className="text-right">
                                        <div className="flex gap-1 justify-end">
                                            <Button variant="ghost" size="sm" onClick={() => router.visit(`/school/report-cards/${rc.id}`)}>View</Button>
                                            {rc.status === 'draft' && <Button variant="ghost" size="sm" className="text-blue-600" onClick={() => handleApprove(rc.id)}>Approve</Button>}
                                            {rc.status === 'approved' && <Button variant="ghost" size="sm" className="text-green-600" onClick={() => handlePublish(rc.id)}>Publish</Button>}
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                    {reportCards.meta && reportCards.meta.last_page > 1 && (
                        <div className="flex justify-between items-center px-4 py-3 border-t">
                            <span className="text-sm text-slate-500">Page {reportCards.meta.current_page} of {reportCards.meta.last_page}</span>
                            <div className="flex gap-2">
                                <Button variant="outline" size="sm" disabled={!reportCards.links.prev} onClick={() => reportCards.links.prev && router.visit(reportCards.links.prev)}>Previous</Button>
                                <Button variant="outline" size="sm" disabled={!reportCards.links.next} onClick={() => reportCards.links.next && router.visit(reportCards.links.next)}>Next</Button>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {/* Generate Dialog */}
            <Dialog open={generateOpen} onOpenChange={setGenerateOpen}>
                <DialogContent className="max-w-lg">
                    <DialogHeader><DialogTitle>Generate Report Cards</DialogTitle></DialogHeader>
                    <form onSubmit={(e) => { e.preventDefault(); genForm.post('/school/report-cards/generate', { onSuccess: () => { genForm.reset(); setGenerateOpen(false); } }); }} className="space-y-4">
                        <div>
                            <Label>Class *</Label>
                            <Select value={genForm.data.class_id} onValueChange={(v) => genForm.setData('class_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select class" /></SelectTrigger>
                                <SelectContent>{classes.map((c) => <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>)}</SelectContent>
                            </Select>
                        </div>
                        <div>
                            <Label>Term *</Label>
                            <Select value={genForm.data.term_id} onValueChange={(v) => genForm.setData('term_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select term" /></SelectTrigger>
                                <SelectContent>{terms.map((t) => <SelectItem key={t.id} value={String(t.id)}>{t.name}</SelectItem>)}</SelectContent>
                            </Select>
                        </div>
                        <div>
                            <Label>Academic Year *</Label>
                            <Select value={genForm.data.academic_year_id} onValueChange={(v) => genForm.setData('academic_year_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select year" /></SelectTrigger>
                                <SelectContent>{academicYears.map((y) => <SelectItem key={y.id} value={String(y.id)}>{y.name}{y.is_current ? ' (Current)' : ''}</SelectItem>)}</SelectContent>
                            </Select>
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setGenerateOpen(false)}>Cancel</Button>
                            <Button type="submit" disabled={genForm.processing}>{genForm.processing ? 'Generating...' : 'Generate'}</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            {/* Bulk Publish Dialog */}
            <Dialog open={bulkOpen} onOpenChange={setBulkOpen}>
                <DialogContent className="max-w-lg">
                    <DialogHeader><DialogTitle>Bulk Publish Report Cards</DialogTitle></DialogHeader>
                    <p className="text-sm text-slate-500">Publish all <strong>approved</strong> report cards for a class and term. Parents will be notified.</p>
                    <form onSubmit={(e) => { e.preventDefault(); setConfirmAction({ type: 'bulk-publish', data: bulkForm.data as Record<string, string> }); }} className="space-y-4">
                        <div>
                            <Label>Class *</Label>
                            <Select value={bulkForm.data.class_id} onValueChange={(v) => bulkForm.setData('class_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select class" /></SelectTrigger>
                                <SelectContent>{classes.map((c) => <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>)}</SelectContent>
                            </Select>
                        </div>
                        <div>
                            <Label>Term *</Label>
                            <Select value={bulkForm.data.term_id} onValueChange={(v) => bulkForm.setData('term_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select term" /></SelectTrigger>
                                <SelectContent>{terms.map((t) => <SelectItem key={t.id} value={String(t.id)}>{t.name}</SelectItem>)}</SelectContent>
                            </Select>
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setBulkOpen(false)}>Cancel</Button>
                            <Button type="submit" className="bg-green-600 hover:bg-green-700 text-white">Publish All</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            {/* Promote Students Dialog */}
            <Dialog open={promoteOpen} onOpenChange={setPromoteOpen}>
                <DialogContent className="max-w-lg">
                    <DialogHeader><DialogTitle>Promote Students</DialogTitle></DialogHeader>
                    <p className="text-sm text-slate-500">Students with GPA ≥ minimum will be promoted to the next class. Others will be retained. This action runs on <strong>published</strong> report cards only.</p>
                    <form onSubmit={(e) => { e.preventDefault(); setConfirmAction({ type: 'promote', data: promoteForm.data as Record<string, string> }); }} className="space-y-4">
                        <div>
                            <Label>Class *</Label>
                            <Select value={promoteForm.data.class_id} onValueChange={(v) => promoteForm.setData('class_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select class" /></SelectTrigger>
                                <SelectContent>{classes.map((c) => <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>)}</SelectContent>
                            </Select>
                        </div>
                        <div>
                            <Label>Term *</Label>
                            <Select value={promoteForm.data.term_id} onValueChange={(v) => promoteForm.setData('term_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select term" /></SelectTrigger>
                                <SelectContent>{terms.map((t) => <SelectItem key={t.id} value={String(t.id)}>{t.name}</SelectItem>)}</SelectContent>
                            </Select>
                        </div>
                        <div>
                            <Label>Academic Year *</Label>
                            <Select value={promoteForm.data.academic_year_id} onValueChange={(v) => promoteForm.setData('academic_year_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select year" /></SelectTrigger>
                                <SelectContent>{academicYears.map((y) => <SelectItem key={y.id} value={String(y.id)}>{y.name}{y.is_current ? ' (Current)' : ''}</SelectItem>)}</SelectContent>
                            </Select>
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setPromoteOpen(false)}>Cancel</Button>
                            <Button type="submit" className="bg-purple-600 hover:bg-purple-700 text-white">Promote Students</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!confirmAction}
                onOpenChange={() => setConfirmAction(null)}
                title={confirmAction?.type === 'approve' ? 'Approve Report Card' : confirmAction?.type === 'publish' ? 'Publish Report Card' : confirmAction?.type === 'bulk-publish' ? 'Bulk Publish' : 'Confirm Promotion'}
                description={
                    confirmAction?.type === 'approve' ? 'This will mark the report card as approved and ready to publish.'
                    : confirmAction?.type === 'publish' ? 'This will publish the report card and notify parents.'
                    : confirmAction?.type === 'bulk-publish' ? 'Publish all approved report cards for this class and term? Parents will be notified.'
                    : 'Promote students based on their GPA. This action cannot be undone.'
                }
                confirmText={confirmAction?.type === 'approve' ? 'Approve' : confirmAction?.type === 'publish' ? 'Publish' : confirmAction?.type === 'bulk-publish' ? 'Publish All' : 'Promote'}
                onConfirm={executeConfirm}
            />
        </AppLayout>
    );
}

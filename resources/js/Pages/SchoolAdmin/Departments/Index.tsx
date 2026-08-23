import { useState } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { Building2, Plus, Pencil, Trash2, ToggleLeft, ToggleRight, Search } from 'lucide-react';
import { useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { PageProps, Department } from '@/Types';

interface Props extends PageProps {
    departments: (Department & { staff_count: number; classes_count: number })[];
    academicDepts: Department[];
    flash?: { success?: string; error?: string };
}

export default function DepartmentsIndex() {
    const { departments, flash } = usePage<Props>().props;
    const [open, setOpen] = useState(false);
    const [editing, setEditing] = useState<Department | null>(null);
    const [search, setSearch] = useState('');
    const [typeFilter, setTypeFilter] = useState('_all');
    const [deleteDialog, setDeleteDialog] = useState<Department | null>(null);

    const emptyForm = { name: '', code: '', description: '', type: 'academic', is_active: true };
    const { data, setData, post, put, processing, errors, reset } = useForm(emptyForm);

    const filtered = departments.filter((d) => {
        if (search && !d.name.toLowerCase().includes(search.toLowerCase()) && !(d.code ?? '').toLowerCase().includes(search.toLowerCase())) return false;
        if (typeFilter !== '_all' && d.type !== typeFilter) return false;
        return true;
    });

    function openCreate() {
        reset(emptyForm);
        setEditing(null);
        setOpen(true);
    }

    function openEdit(dept: Department) {
        setData({ name: dept.name, code: dept.code ?? '', description: dept.description ?? '', type: dept.type ?? 'academic', is_active: dept.is_active ?? true });
        setEditing(dept);
        setOpen(true);
    }

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (editing) {
            put(`/school/departments/${editing.id}`, {
                onSuccess: () => { reset(); setOpen(false); setEditing(null); },
            });
        } else {
            post('/school/departments', {
                onSuccess: () => { reset(); setOpen(false); },
            });
        }
    }

    const toggleStatus = (dept: Department) => {
        router.post(`/school/departments/${dept.id}/toggle-status`);
    };

    const confirmDelete = () => {
        if (!deleteDialog) return;
        router.delete(`/school/departments/${deleteDialog.id}`, {
            onError: () => setDeleteDialog(null),
            onSuccess: () => setDeleteDialog(null),
        });
    };

    return (
        <AppLayout breadcrumbs={[{ label: 'Academics' }, { label: 'Departments' }]}>
            <Head title="Departments" />

            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div>
                    <h1 className="text-xl font-bold text-slate-900 dark:text-white">Departments</h1>
                    <p className="text-sm text-slate-500 mt-0.5">{departments.length} total &middot; {departments.filter(d => d.is_active).length} active</p>
                </div>
                <Button onClick={openCreate} className="bg-indigo-600 hover:bg-indigo-700 text-white inline-flex items-center gap-2">
                    <Plus className="w-4 h-4" /> Add Department
                </Button>
            </div>

            {flash?.success && (
                <div className="flex items-center gap-2 p-3 rounded-lg bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 mb-4">
                    <p className="text-sm text-green-700 dark:text-green-300">{flash.success}</p>
                </div>
            )}

            <div className="flex flex-col sm:flex-row gap-3 mb-4">
                <div className="relative flex-1">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <Input placeholder="Search departments..." value={search} onChange={(e) => setSearch(e.target.value)} className="pl-9 h-9" />
                </div>
                <Select value={typeFilter} onValueChange={(v) => setTypeFilter(v)}>
                    <SelectTrigger className="w-full sm:w-44 h-9"><SelectValue placeholder="All Types" /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="_all">All Types</SelectItem>
                        <SelectItem value="academic">Academic (SSS)</SelectItem>
                        <SelectItem value="staff">Staff / HR</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <Card className="dark:bg-slate-900 border-slate-200 dark:border-slate-800">
                <CardContent className="p-0 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHead>Name</TableHead>
                                <TableHead className="hidden sm:table-cell">Code</TableHead>
                                <TableHead className="hidden md:table-cell">Type</TableHead>
                                <TableHead className="hidden sm:table-cell">Staff</TableHead>
                                <TableHead className="hidden sm:table-cell">Classes</TableHead>
                                <TableHead className="hidden lg:table-cell">Status</TableHead>
                                <TableHead className="w-24" />
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={7} className="text-center py-12 text-slate-400">
                                        <Building2 className="w-10 h-10 mx-auto mb-2 opacity-40" />
                                        No departments found.
                                    </TableCell>
                                </TableRow>
                            ) : filtered.map((dept) => (
                                <TableRow key={dept.id}>
                                    <TableCell className="font-medium text-slate-900 dark:text-white">{dept.name}</TableCell>
                                    <TableCell className="hidden sm:table-cell">
                                        {dept.code ? <Badge variant="outline">{dept.code}</Badge> : <span className="text-slate-400">—</span>}
                                    </TableCell>
                                    <TableCell className="hidden md:table-cell">
                                        <Badge variant="secondary" className={`text-[10px] ${dept.type === 'academic' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'}`}>
                                            {dept.type === 'academic' ? 'Academic (SSS)' : 'Staff / HR'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell className="hidden sm:table-cell text-sm text-slate-500">{dept.staff_count ?? 0}</TableCell>
                                    <TableCell className="hidden sm:table-cell text-sm text-slate-500">{dept.classes_count ?? 0}</TableCell>
                                    <TableCell className="hidden lg:table-cell">
                                        <Badge variant={dept.is_active ? 'default' : 'secondary'} className={dept.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ''}>
                                            {dept.is_active ? 'Active' : 'Inactive'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center gap-0.5">
                                            <Button variant="ghost" size="icon" className="h-7 w-7" onClick={() => openEdit(dept)}>
                                                <Pencil className="w-3.5 h-3.5" />
                                            </Button>
                                            <Button variant="ghost" size="icon" className="h-7 w-7" onClick={() => toggleStatus(dept)}>
                                                {dept.is_active ? <ToggleRight className="w-4 h-4 text-emerald-500" /> : <ToggleLeft className="w-4 h-4 text-slate-400" />}
                                            </Button>
                                            <Button variant="ghost" size="icon" className="h-7 w-7 text-red-500 hover:text-red-600" onClick={() => setDeleteDialog(dept)}>
                                                <Trash2 className="w-3.5 h-3.5" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>{editing ? 'Edit Department' : 'Add Department'}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="space-y-4 mt-2">
                        <div className="space-y-1.5">
                            <Label>Name <span className="text-red-500">*</span></Label>
                            <Input value={data.name} onChange={e => setData('name', e.target.value)} placeholder="e.g. Science, Arts, Commercial" />
                            {errors.name && <p className="text-xs text-red-500">{errors.name}</p>}
                        </div>
                        <div className="space-y-1.5">
                            <Label>Code</Label>
                            <Input value={data.code} onChange={e => setData('code', e.target.value)} placeholder="e.g. SCI" />
                            {errors.code && <p className="text-xs text-red-500">{errors.code}</p>}
                        </div>
                        <div className="space-y-1.5">
                            <Label>Type <span className="text-red-500">*</span></Label>
                            <Select value={data.type} onValueChange={(v) => setData('type', v)}>
                                <SelectTrigger><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="academic">Academic (SSS Departments)</SelectItem>
                                    <SelectItem value="staff">Staff / HR</SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.type && <p className="text-xs text-red-500">{errors.type}</p>}
                        </div>
                        <div className="space-y-1.5">
                            <Label>Description</Label>
                            <Textarea value={data.description} onChange={e => setData('description', e.target.value)} rows={3} placeholder="Optional description..." />
                        </div>
                        <div className="flex items-center gap-2">
                            <input type="checkbox" id="dept_active" className="rounded border-slate-300" checked={data.is_active} onChange={(e) => setData('is_active', e.target.checked)} />
                            <Label htmlFor="dept_active" className="cursor-pointer">Active</Label>
                        </div>
                        <DialogFooter>
                            <Button type="button" variant="ghost" onClick={() => setOpen(false)}>Cancel</Button>
                            <Button type="submit" disabled={processing} className="bg-indigo-600 hover:bg-indigo-700 text-white">
                                {processing ? 'Saving...' : editing ? 'Update' : 'Create'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Dialog open={deleteDialog !== null} onOpenChange={(open) => { if (!open) setDeleteDialog(null); }}>
                <DialogContent className="sm:max-w-sm">
                    <DialogHeader>
                        <DialogTitle>Delete Department</DialogTitle>
                    </DialogHeader>
                    <p className="text-sm text-slate-600 dark:text-slate-400">
                        Are you sure you want to delete <strong>{deleteDialog?.name}</strong>?
                        {((deleteDialog?.staff_count ?? 0) > 0 || (deleteDialog?.classes_count ?? 0) > 0)
                            ? ' This department has dependencies and cannot be deleted. Try deactivating it instead.'
                            : ' This action cannot be undone.'}
                    </p>
                    <DialogFooter>
                        <Button variant="outline" size="sm" onClick={() => setDeleteDialog(null)}>Cancel</Button>
                        <Button size="sm" variant="destructive" onClick={confirmDelete}
                            disabled={(deleteDialog?.staff_count ?? 0) > 0 || (deleteDialog?.classes_count ?? 0) > 0}>
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}

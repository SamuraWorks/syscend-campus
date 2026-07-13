import { useState } from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { cn } from '@/lib/utils';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { BookOpen, ChevronRight, CheckCircle2, Clock, AlertCircle } from 'lucide-react';

interface HomeworkItem { id: number; title: string; description: string | null; class_name: string; section: string | null; subject: string; assigned_date: string; due_date: string; status: string; total_submitted: number; total_students: number; }
interface ClassOption { id: string; label: string; }
interface Summary { total: number; pending: number; completed: number; overdue: number; }
interface Props { linked: boolean; classes: ClassOption[]; selectedClassId: string | null; homework: HomeworkItem[]; summary: Summary; }

export default function Homework({ linked, classes, selectedClassId, homework, summary }: Props) {
    const [selectedClass, setSelectedClass] = useState(selectedClassId ?? '');

    if (!linked) {
        return (
            <AppLayout title="Homework">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <BookOpen className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">Account not linked</h2>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout title="Homework">
            <div className="space-y-6">
                <div className="flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/school/principal/dashboard" className="hover:text-slate-700 dark:hover:text-slate-300">Principal</Link>
                    <ChevronRight className="w-3 h-3" />
                    <span className="text-slate-700 dark:text-slate-300 font-medium">Homework</span>
                </div>

                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <BookOpen className="w-5 h-5 text-violet-500" /> Homework
                        </h1>
                        <p className="text-sm text-slate-500 mt-0.5">Monitor homework assignments</p>
                    </div>
                    {classes.length > 0 && (
                        <Select value={selectedClass} onValueChange={v => setSelectedClass(v)}>
                            <SelectTrigger className="w-[180px] h-9"><SelectValue placeholder="All Classes" /></SelectTrigger>
                            <SelectContent>
                                {classes.map(c => <SelectItem key={c.id} value={c.id}>{c.label}</SelectItem>)}
                            </SelectContent>
                        </Select>
                    )}
                </div>

                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-slate-100 dark:bg-slate-800"><BookOpen className="w-4 h-4 text-slate-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.total}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Total Assigned</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-blue-100 dark:bg-blue-900/30"><Clock className="w-4 h-4 text-blue-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.pending}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Pending</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-green-100 dark:bg-green-900/30"><CheckCircle2 className="w-4 h-4 text-green-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.completed}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Completed</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-red-100 dark:bg-red-900/30"><AlertCircle className="w-4 h-4 text-red-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.overdue}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Overdue</p>
                        </CardContent>
                    </Card>
                </div>

                {homework.length === 0 ? (
                    <Card><CardContent className="py-16 text-center"><BookOpen className="w-10 h-10 mx-auto mb-3 text-slate-300" /><p className="text-sm text-slate-400">No homework assignments found.</p></CardContent></Card>
                ) : (
                    <div className="space-y-3">
                        {homework.map(hw => (
                            <Card key={hw.id}>
                                <CardContent className="p-4">
                                    <div className="flex items-start justify-between gap-4">
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center gap-2 flex-wrap">
                                                <h3 className="text-sm font-semibold text-slate-800 dark:text-slate-200">{hw.title}</h3>
                                                <Badge variant="secondary" className={cn('text-[10px] capitalize', hw.status === 'completed' ? 'bg-green-100 text-green-700' : hw.status === 'overdue' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')}>{hw.status}</Badge>
                                            </div>
                                            {hw.description && <p className="text-xs text-slate-500 mt-1 line-clamp-2">{hw.description}</p>}
                                            <div className="flex items-center gap-3 mt-2 text-xs text-slate-400">
                                                <span>{hw.class_name}{hw.section ? ` — ${hw.section}` : ''}</span>
                                                <span>·</span>
                                                <span>{hw.subject}</span>
                                                <span>·</span>
                                                <span>Due: {hw.due_date}</span>
                                            </div>
                                        </div>
                                        <div className="text-right shrink-0">
                                            <p className="text-sm font-bold text-slate-800 dark:text-slate-200">{hw.total_submitted}/{hw.total_students}</p>
                                            <p className="text-[10px] text-slate-400">Submitted</p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

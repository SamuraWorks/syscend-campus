import { useState } from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { cn } from '@/lib/utils';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { CalendarCheck, ChevronRight, CheckCircle2, XCircle, Clock, Users } from 'lucide-react';

interface AttendanceRecord { student_id: number; full_name: string; status: string; note: string | null; }
interface ClassOption { id: string; label: string; }
interface DayData { date: string; records: AttendanceRecord[]; }
interface Summary { present: number; absent: number; late: number; total: number; rate: number; }
interface Props {
    linked: boolean; classes: ClassOption[];
    selectedClassId: string | null; attendanceDays: DayData[];
    summary: Summary;
}

export default function Attendance({ linked, classes, selectedClassId, attendanceDays, summary }: Props) {
    const [selectedClass, setSelectedClass] = useState(selectedClassId ?? '');

    if (!linked) {
        return (
            <AppLayout title="Attendance">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <CalendarCheck className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">Account not linked</h2>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout title="Attendance">
            <div className="space-y-6">
                <div className="flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/school/principal/dashboard" className="hover:text-slate-700 dark:hover:text-slate-300">Principal</Link>
                    <ChevronRight className="w-3 h-3" />
                    <span className="text-slate-700 dark:text-slate-300 font-medium">Attendance</span>
                </div>

                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <CalendarCheck className="w-5 h-5 text-green-500" /> Attendance
                        </h1>
                        <p className="text-sm text-slate-500 mt-0.5">Track student attendance across classes</p>
                    </div>
                    <div className="flex items-center gap-2">
                        <Select value={selectedClass} onValueChange={v => setSelectedClass(v)}>
                            <SelectTrigger className="w-[180px] h-9"><SelectValue placeholder="All Classes" /></SelectTrigger>
                            <SelectContent>
                                {classes.map(c => <SelectItem key={c.id} value={c.id}>{c.label}</SelectItem>)}
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card>
                        <CardContent className="p-4">
                            <div className="flex items-center justify-between mb-2">
                                <div className="w-8 h-8 rounded-lg flex items-center justify-center bg-green-100 dark:bg-green-900/30"><CheckCircle2 className="w-4 h-4 text-green-600" /></div>
                            </div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.present}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Present</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="flex items-center justify-between mb-2">
                                <div className="w-8 h-8 rounded-lg flex items-center justify-center bg-red-100 dark:bg-red-900/30"><XCircle className="w-4 h-4 text-red-600" /></div>
                            </div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.absent}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Absent</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="flex items-center justify-between mb-2">
                                <div className="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-100 dark:bg-amber-900/30"><Clock className="w-4 h-4 text-amber-600" /></div>
                            </div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.late}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Late</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="flex items-center justify-between mb-2">
                                <div className="w-8 h-8 rounded-lg flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/30"><Users className="w-4 h-4 text-indigo-600" /></div>
                            </div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.rate}%</p>
                            <p className="text-xs text-slate-500 mt-0.5">Attendance Rate</p>
                        </CardContent>
                    </Card>
                </div>

                {attendanceDays.length === 0 ? (
                    <Card><CardContent className="py-16 text-center"><CalendarCheck className="w-10 h-10 mx-auto mb-3 text-slate-300" /><p className="text-sm text-slate-400">No attendance data available.</p></CardContent></Card>
                ) : (
                    <div className="space-y-4">
                        {attendanceDays.map(day => (
                            <Card key={day.date}>
                                <div className="p-4 border-b border-slate-100 dark:border-slate-800">
                                    <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">{day.date}</h2>
                                </div>
                                <CardContent className="p-0">
                                    <Table>
                                        <TableHeader>
                                            <TableRow className="bg-slate-50 dark:bg-slate-900">
                                                <TableHead>Student</TableHead>
                                                <TableHead className="text-center">Status</TableHead>
                                                <TableHead>Note</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {day.records.map(r => (
                                                <TableRow key={r.student_id}>
                                                    <TableCell className="font-medium text-sm text-slate-900 dark:text-white">{r.full_name}</TableCell>
                                                    <TableCell className="text-center">
                                                        <Badge variant="secondary" className={cn('text-[10px] capitalize', r.status === 'present' ? 'bg-green-100 text-green-700' : r.status === 'absent' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')}>
                                                            {r.status}
                                                        </Badge>
                                                    </TableCell>
                                                    <TableCell className="text-sm text-slate-500">{r.note ?? '—'}</TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

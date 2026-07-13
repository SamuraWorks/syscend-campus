import { useState } from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { cn } from '@/lib/utils';
import { Card, CardContent } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Clock, ChevronRight, BookOpen } from 'lucide-react';

interface TimetableSlot { id: number; day: string; time_start: string; time_end: string; subject: string; teacher: string | null; room: string | null; }
interface ClassOption { id: string; label: string; }
interface Props { linked: boolean; classes: ClassOption[]; selectedClassId: string | null; timetable: TimetableSlot[]; }

const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

export default function Timetable({ linked, classes, selectedClassId, timetable }: Props) {
    const [selectedClass, setSelectedClass] = useState(selectedClassId ?? '');

    if (!linked) {
        return (
            <AppLayout title="Timetable">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <Clock className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">Account not linked</h2>
                </div>
            </AppLayout>
        );
    }

    const grouped = new Map<string, TimetableSlot[]>();
    for (const slot of timetable) {
        if (!grouped.has(slot.day)) grouped.set(slot.day, []);
        grouped.get(slot.day)!.push(slot);
    }
    for (const [, slots] of grouped) slots.sort((a, b) => a.time_start.localeCompare(b.time_start));

    return (
        <AppLayout title="Timetable">
            <div className="space-y-6">
                <div className="flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/school/principal/dashboard" className="hover:text-slate-700 dark:hover:text-slate-300">Principal</Link>
                    <ChevronRight className="w-3 h-3" />
                    <span className="text-slate-700 dark:text-slate-300 font-medium">Timetable</span>
                </div>

                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <Clock className="w-5 h-5 text-indigo-500" /> Timetable
                        </h1>
                        <p className="text-sm text-slate-500 mt-0.5">Class schedule and timetable</p>
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

                {timetable.length === 0 ? (
                    <Card><CardContent className="py-16 text-center"><Clock className="w-10 h-10 mx-auto mb-3 text-slate-300" /><p className="text-sm text-slate-400">No timetable data available.</p></CardContent></Card>
                ) : (
                    <div className="space-y-4">
                        {DAYS.map(day => {
                            const slots = grouped.get(day);
                            if (!slots || slots.length === 0) return null;
                            return (
                                <Card key={day}>
                                    <div className="p-4 border-b border-slate-100 dark:border-slate-800">
                                        <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">{day}</h2>
                                    </div>
                                    <CardContent className="p-0">
                                        <div className="divide-y divide-slate-50 dark:divide-slate-800/50">
                                            {slots.map(s => (
                                                <div key={s.id} className="flex items-center gap-4 px-4 py-3">
                                                    <div className="w-20 shrink-0">
                                                        <p className="text-xs font-mono text-slate-500">{s.time_start}</p>
                                                        <p className="text-[10px] text-slate-400">to {s.time_end}</p>
                                                    </div>
                                                    <div className="w-px h-8 bg-slate-200 dark:bg-slate-700 shrink-0" />
                                                    <div className="flex-1 min-w-0">
                                                        <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{s.subject}</p>
                                                        <div className="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                                                            {s.teacher && <span>{s.teacher}</span>}
                                                            {s.room && <span>· Room {s.room}</span>}
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </CardContent>
                                </Card>
                            );
                        })}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

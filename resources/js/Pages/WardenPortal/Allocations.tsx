import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Users } from 'lucide-react';

interface Allocation {
    id: number; student_name: string; admission_no: string;
    room_no: string; bed_no: string | null;
    joining_date: string | null; leaving_date: string | null;
    status: string; notes: string | null;
}
interface Props {
    linked: boolean;
    allocations: Allocation[];
}

const statusColor: Record<string, string> = {
    active: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    inactive: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    pending: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
};

export default function WardenAllocations({ linked, allocations }: Props) {
    if (!linked) {
        return (
            <AppLayout title="Allocations">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <Users className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">Account not linked</h2>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout title="Allocations">
            <div className="space-y-6">
                <div>
                    <h1 className="text-xl font-bold text-slate-900 dark:text-white">Allocations</h1>
                    <p className="text-sm text-slate-500">{allocations.length} allocation{allocations.length !== 1 ? 's' : ''}</p>
                </div>

                <Card>
                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Admission No</TableHead>
                                    <TableHead>Room</TableHead>
                                    <TableHead>Bed</TableHead>
                                    <TableHead>Joining Date</TableHead>
                                    <TableHead>Leaving Date</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {allocations.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={7} className="text-center py-8 text-slate-400">No allocations found.</TableCell>
                                    </TableRow>
                                ) : (
                                    allocations.map(a => (
                                        <TableRow key={a.id}>
                                            <TableCell className="font-medium">{a.student_name}</TableCell>
                                            <TableCell className="text-xs text-slate-500">{a.admission_no}</TableCell>
                                            <TableCell>{a.room_no}</TableCell>
                                            <TableCell className="text-xs">{a.bed_no ?? '—'}</TableCell>
                                            <TableCell className="text-xs">{a.joining_date ?? '—'}</TableCell>
                                            <TableCell className="text-xs">{a.leaving_date ?? '—'}</TableCell>
                                            <TableCell><Badge className={cn('text-[10px]', statusColor[a.status] ?? 'bg-slate-100 text-slate-600')}>{a.status}</Badge></TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

function cn(...classes: (string | boolean | undefined | null)[]) {
    return classes.filter(Boolean).join(' ');
}

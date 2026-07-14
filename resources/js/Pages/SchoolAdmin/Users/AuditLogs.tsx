import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ArrowLeft, ShieldCheck } from 'lucide-react';

interface AuditLog { id: number; action: string; description: string; created_at: string; performer?: { id: number; name: string }; }
interface User { id: number; name: string; email: string; }
interface PageProps { user: User; logs: { data: AuditLog[]; meta: { total: number; per_page: number; current_page: number; last_page: number } } }

export default function AuditLogs({ user, logs }: PageProps) {
    return (
        <AppLayout header={<h1 className="text-2xl font-bold">Audit Logs</h1>}>
            <Head title={`Audit Logs - ${user.name}`} />
            <div className="max-w-6xl mx-auto py-6 px-4 space-y-6">
                <div className="flex items-center gap-3">
                    <Link href={route('school.settings.admins')} className="text-muted-foreground hover:text-foreground"><ArrowLeft className="h-4 w-4" /></Link>
                    <h2 className="text-lg font-semibold">Audit Log for {user.name}</h2>
                </div>
                <Card>
                    <CardHeader><CardTitle className="flex items-center gap-2"><ShieldCheck className="h-5 w-5" /> Activity Log ({logs.meta.total} entries)</CardTitle></CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Action</TableHead>
                                    <TableHead>Description</TableHead>
                                    <TableHead>Performer</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {logs.data.length === 0 ? (
                                    <TableRow><TableCell colSpan={4} className="text-center py-8 text-muted-foreground">No audit logs found.</TableCell></TableRow>
                                ) : logs.data.map(log => (
                                    <TableRow key={log.id}>
                                        <TableCell className="text-sm text-muted-foreground">{new Date(log.created_at).toLocaleString()}</TableCell>
                                        <TableCell><Badge variant="outline">{log.action}</Badge></TableCell>
                                        <TableCell className="text-sm max-w-md truncate">{log.description}</TableCell>
                                        <TableCell className="text-sm">{log.performer?.name ?? 'System'}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

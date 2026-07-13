import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Building, MapPin, BedDouble, Users } from 'lucide-react';

interface HostelInfo {
    id: number; name: string; type: string | null; address: string | null;
    total_rooms: number; total_capacity: number; occupied: number; status: string;
}
interface Props {
    linked: boolean;
    hostel: HostelInfo | null;
}

export default function WardenHostel({ linked, hostel }: Props) {
    if (!linked) {
        return (
            <AppLayout title="Hostel">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <Building className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">Account not linked</h2>
                </div>
            </AppLayout>
        );
    }

    if (!hostel) {
        return (
            <AppLayout title="Hostel">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <Building className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">No hostel assigned</h2>
                </div>
            </AppLayout>
        );
    }

    const occupancyRate = hostel.total_capacity > 0 ? Math.round((hostel.occupied / hostel.total_capacity) * 100) : 0;

    return (
        <AppLayout title="Hostel Details">
            <div className="space-y-6">
                <div>
                    <h1 className="text-xl font-bold text-slate-900 dark:text-white">{hostel.name}</h1>
                    <p className="text-sm text-slate-500">{hostel.type ?? 'Hostel'} · {hostel.address ?? '—'}</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                    <Card>
                        <CardContent className="p-4">
                            <div className="flex items-center gap-2 mb-2">
                                <div className="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <BedDouble className="w-4 h-4 text-blue-600" />
                                </div>
                            </div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{hostel.total_rooms}</p>
                            <p className="text-xs text-slate-500">Total Rooms</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="flex items-center gap-2 mb-2">
                                <div className="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <Users className="w-4 h-4 text-emerald-600" />
                                </div>
                            </div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{hostel.total_capacity}</p>
                            <p className="text-xs text-slate-500">Total Capacity</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="flex items-center gap-2 mb-2">
                                <div className="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                    <Users className="w-4 h-4 text-amber-600" />
                                </div>
                            </div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{hostel.occupied}</p>
                            <p className="text-xs text-slate-500">Occupied</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="flex items-center gap-2 mb-2">
                                <div className="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                                    <Building className="w-4 h-4 text-violet-600" />
                                </div>
                            </div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{occupancyRate}%</p>
                            <p className="text-xs text-slate-500">Occupancy Rate</p>
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <MapPin className="w-4 h-4 text-blue-500" /> Hostel Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <p className="text-xs text-slate-500">Name</p>
                                <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{hostel.name}</p>
                            </div>
                            <div>
                                <p className="text-xs text-slate-500">Type</p>
                                <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{hostel.type ?? '—'}</p>
                            </div>
                            <div>
                                <p className="text-xs text-slate-500">Address</p>
                                <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{hostel.address ?? '—'}</p>
                            </div>
                            <div>
                                <p className="text-xs text-slate-500">Status</p>
                                <Badge className={cn('text-[10px]', hostel.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400')}>{hostel.status}</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

function cn(...classes: (string | boolean | undefined | null)[]) {
    return classes.filter(Boolean).join(' ');
}

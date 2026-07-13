import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent } from '@/components/ui/card';
import { BarChart3, ChevronRight, TrendingUp, Users, BookOpen, Calendar } from 'lucide-react';

interface ReportLink { title: string; description: string; href: string; icon: string; color: string; }
interface Summary { total_students: number; total_staff: number; attendance_rate: number; pass_rate: number; }
interface Props { linked: boolean; reportLinks: ReportLink[]; summary: Summary; }

const iconMap: Record<string, any> = {
    'bar-chart': BarChart3, 'trending-up': TrendingUp, users: Users, 'book-open': BookOpen, calendar: Calendar,
};
const colorMap: Record<string, string> = {
    blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600',
    green: 'bg-green-100 dark:bg-green-900/30 text-green-600',
    purple: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600',
    indigo: 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600',
    amber: 'bg-amber-100 dark:bg-amber-900/30 text-amber-600',
};

export default function Reports({ linked, reportLinks, summary }: Props) {
    if (!linked) {
        return (
            <AppLayout title="Reports">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <BarChart3 className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">Account not linked</h2>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout title="Reports">
            <div className="space-y-6">
                <div className="flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/school/principal/dashboard" className="hover:text-slate-700 dark:hover:text-slate-300">Principal</Link>
                    <ChevronRight className="w-3 h-3" />
                    <span className="text-slate-700 dark:text-slate-300 font-medium">Reports</span>
                </div>

                <div>
                    <h1 className="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <BarChart3 className="w-5 h-5 text-cyan-500" /> Reports
                    </h1>
                    <p className="text-sm text-slate-500 mt-0.5">School performance reports and analytics</p>
                </div>

                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-indigo-100 dark:bg-indigo-900/30"><Users className="w-4 h-4 text-indigo-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.total_students}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Students</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-violet-100 dark:bg-violet-900/30"><Users className="w-4 h-4 text-violet-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.total_staff}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Staff</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-green-100 dark:bg-green-900/30"><Calendar className="w-4 h-4 text-green-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.attendance_rate}%</p>
                            <p className="text-xs text-slate-500 mt-0.5">Attendance Rate</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-cyan-100 dark:bg-cyan-900/30"><TrendingUp className="w-4 h-4 text-cyan-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.pass_rate}%</p>
                            <p className="text-xs text-slate-500 mt-0.5">Pass Rate</p>
                        </CardContent>
                    </Card>
                </div>

                {reportLinks.length === 0 ? (
                    <Card><CardContent className="py-16 text-center"><BarChart3 className="w-10 h-10 mx-auto mb-3 text-slate-300" /><p className="text-sm text-slate-400">No reports available.</p></CardContent></Card>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {reportLinks.map((r, i) => {
                            const Icon = iconMap[r.icon] || BarChart3;
                            const color = colorMap[r.color] || colorMap.blue;
                            return (
                                <Link key={i} href={r.href}>
                                    <Card className="hover:shadow-md transition-shadow cursor-pointer h-full">
                                        <CardContent className="p-5">
                                            <div className={cn('w-10 h-10 rounded-xl flex items-center justify-center mb-3', color)}>
                                                <Icon className="w-5 h-5" />
                                            </div>
                                            <h3 className="text-sm font-semibold text-slate-800 dark:text-slate-200">{r.title}</h3>
                                            <p className="text-xs text-slate-500 mt-1">{r.description}</p>
                                        </CardContent>
                                    </Card>
                                </Link>
                            );
                        })}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

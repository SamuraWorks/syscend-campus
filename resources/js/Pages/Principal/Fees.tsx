import { useState } from 'react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { cn } from '@/lib/utils';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { DollarSign, ChevronRight, CheckCircle2, AlertCircle, TrendingUp } from 'lucide-react';

interface FeeOverview { class_name: string; section: string | null; total_students: number; collected: number; pending: number; collection_rate: number; }
interface RecentPayment { id: number; student_name: string; class_name: string; amount: number; date: string; status: string; }
interface Summary { total_fees: number; total_collected: number; total_pending: number; collection_rate: number; }
interface Props { linked: boolean; feeOverview: FeeOverview[]; recentPayments: RecentPayment[]; summary: Summary; }

export default function Fees({ linked, feeOverview, recentPayments, summary }: Props) {
    if (!linked) {
        return (
            <AppLayout title="Fees">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <DollarSign className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">Account not linked</h2>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout title="Fees">
            <div className="space-y-6">
                <div className="flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/school/principal/dashboard" className="hover:text-slate-700 dark:hover:text-slate-300">Principal</Link>
                    <ChevronRight className="w-3 h-3" />
                    <span className="text-slate-700 dark:text-slate-300 font-medium">Fees</span>
                </div>

                <div>
                    <h1 className="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <DollarSign className="w-5 h-5 text-emerald-500" /> Fees Overview
                    </h1>
                    <p className="text-sm text-slate-500 mt-0.5">Fee collection and payment tracking</p>
                </div>

                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-emerald-100 dark:bg-emerald-900/30"><DollarSign className="w-4 h-4 text-emerald-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">Le {summary.total_fees.toLocaleString()}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Total Fees</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-green-100 dark:bg-green-900/30"><CheckCircle2 className="w-4 h-4 text-green-600" /></div>
                            <p className="text-2xl font-bold text-green-600">Le {summary.total_collected.toLocaleString()}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Collected</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-red-100 dark:bg-red-900/30"><AlertCircle className="w-4 h-4 text-red-600" /></div>
                            <p className="text-2xl font-bold text-red-600">Le {summary.total_pending.toLocaleString()}</p>
                            <p className="text-xs text-slate-500 mt-0.5">Pending</p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="p-4">
                            <div className="w-8 h-8 rounded-lg flex items-center justify-center mb-2 bg-indigo-100 dark:bg-indigo-900/30"><TrendingUp className="w-4 h-4 text-indigo-600" /></div>
                            <p className="text-2xl font-bold text-slate-900 dark:text-white">{summary.collection_rate}%</p>
                            <p className="text-xs text-slate-500 mt-0.5">Collection Rate</p>
                        </CardContent>
                    </Card>
                </div>

                {feeOverview.length > 0 && (
                    <Card>
                        <div className="p-4 border-b border-slate-100 dark:border-slate-800">
                            <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Class-wise Collection</h2>
                        </div>
                        <CardContent className="p-0">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="text-xs uppercase text-slate-400 border-b border-slate-100 dark:border-slate-800">
                                        <th className="text-left py-3 px-4 font-medium">Class</th>
                                        <th className="text-center py-3 px-4 font-medium">Students</th>
                                        <th className="text-right py-3 px-4 font-medium">Collected</th>
                                        <th className="text-right py-3 px-4 font-medium">Pending</th>
                                        <th className="text-right py-3 px-4 font-medium">Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {feeOverview.map(f => (
                                        <tr key={`${f.class_name}-${f.section}`} className="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                            <td className="py-3 px-4 font-medium text-slate-900 dark:text-white">{f.class_name}{f.section ? ` — ${f.section}` : ''}</td>
                                            <td className="py-3 px-4 text-center text-slate-500">{f.total_students}</td>
                                            <td className="py-3 px-4 text-right text-green-600 font-medium">Le {f.collected.toLocaleString()}</td>
                                            <td className="py-3 px-4 text-right text-red-500 font-medium">Le {f.pending.toLocaleString()}</td>
                                            <td className="py-3 px-4 text-right">
                                                <Badge variant="outline" className={cn('text-[10px]', f.collection_rate >= 80 ? 'text-green-600' : f.collection_rate >= 50 ? 'text-amber-600' : 'text-red-600')}>{f.collection_rate}%</Badge>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </CardContent>
                    </Card>
                )}

                {recentPayments.length > 0 && (
                    <Card>
                        <div className="p-4 border-b border-slate-100 dark:border-slate-800">
                            <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Recent Payments</h2>
                        </div>
                        <CardContent className="p-0">
                            <div className="divide-y divide-slate-100 dark:divide-slate-800">
                                {recentPayments.map(p => (
                                    <div key={p.id} className="flex items-center gap-3 px-4 py-3">
                                        <div className="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-xs font-bold text-green-600 shrink-0">
                                            <CheckCircle2 className="w-4 h-4" />
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">{p.student_name}</p>
                                            <p className="text-xs text-slate-400">{p.class_name} · {p.date}</p>
                                        </div>
                                        <div className="text-right shrink-0">
                                            <p className="text-sm font-bold text-green-600">Le {p.amount.toLocaleString()}</p>
                                            <Badge variant="secondary" className="text-[10px] capitalize">{p.status}</Badge>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}

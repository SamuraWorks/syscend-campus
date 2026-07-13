import { Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent } from '@/components/ui/card';
import { User, ChevronRight, Mail, Phone, Building, Shield, Key } from 'lucide-react';

interface Props {
    linked: boolean;
    profile: {
        id: number; name: string; email: string | null; phone: string | null;
        role: string; school_name: string | null; photo_url: string | null;
        created_at: string;
    };
}

export default function Profile({ linked, profile }: Props) {
    if (!linked) {
        return (
            <AppLayout title="Profile">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <User className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">Account not linked</h2>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout title="Profile">
            <div className="space-y-6">
                <div className="flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/school/principal/dashboard" className="hover:text-slate-700 dark:hover:text-slate-300">Principal</Link>
                    <ChevronRight className="w-3 h-3" />
                    <span className="text-slate-700 dark:text-slate-300 font-medium">Profile</span>
                </div>

                <div>
                    <h1 className="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <User className="w-5 h-5 text-indigo-500" /> My Profile
                    </h1>
                    <p className="text-sm text-slate-500 mt-0.5">Your account information</p>
                </div>

                <div className="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white flex items-center gap-5">
                    <div className="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center overflow-hidden shrink-0">
                        {profile.photo_url
                            ? <img src={profile.photo_url} alt={profile.name} className="w-full h-full object-cover" />
                            : <User className="w-10 h-10 text-white/80" />}
                    </div>
                    <div>
                        <h2 className="text-xl font-bold">{profile.name}</h2>
                        <p className="text-white/80 text-sm capitalize">{profile.role}</p>
                        {profile.school_name && <p className="text-white/70 text-xs mt-0.5">{profile.school_name}</p>}
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <Card>
                        <CardContent className="p-5 space-y-4">
                            <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                <User className="w-4 h-4 text-indigo-500" /> Personal Information
                            </h3>
                            <div className="space-y-3">
                                <div className="flex items-center gap-3">
                                    <User className="w-4 h-4 text-slate-400 shrink-0" />
                                    <div>
                                        <p className="text-xs text-slate-500">Full Name</p>
                                        <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{profile.name}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Mail className="w-4 h-4 text-slate-400 shrink-0" />
                                    <div>
                                        <p className="text-xs text-slate-500">Email</p>
                                        <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{profile.email ?? '—'}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Phone className="w-4 h-4 text-slate-400 shrink-0" />
                                    <div>
                                        <p className="text-xs text-slate-500">Phone</p>
                                        <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{profile.phone ?? '—'}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardContent className="p-5 space-y-4">
                            <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                <Shield className="w-4 h-4 text-violet-500" /> Account Details
                            </h3>
                            <div className="space-y-3">
                                <div className="flex items-center gap-3">
                                    <Shield className="w-4 h-4 text-slate-400 shrink-0" />
                                    <div>
                                        <p className="text-xs text-slate-500">Role</p>
                                        <p className="text-sm font-medium text-slate-800 dark:text-slate-200 capitalize">{profile.role}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Building className="w-4 h-4 text-slate-400 shrink-0" />
                                    <div>
                                        <p className="text-xs text-slate-500">School</p>
                                        <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{profile.school_name ?? '—'}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-3">
                                    <Key className="w-4 h-4 text-slate-400 shrink-0" />
                                    <div>
                                        <p className="text-xs text-slate-500">Member Since</p>
                                        <p className="text-sm font-medium text-slate-800 dark:text-slate-200">{profile.created_at}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

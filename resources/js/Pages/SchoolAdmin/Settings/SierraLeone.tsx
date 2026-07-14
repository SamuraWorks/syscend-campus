import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { toast } from 'sonner';

interface School { id: number; name: string; country: string; currency: string; currency_symbol: string; }
interface Department { id: number; name: string; code: string; }
interface PageProps { school: School; settings: Record<string, string>; levels: string[]; departments: Department[]; examTypes: string[] }

export default function SierraLeoneSettings({ school, settings, levels, departments, examTypes }: PageProps) {
    const { data, setData, post, processing } = useForm({
        school_level: settings.school_level || '',
        grading_system: settings.grading_system || 'ugce',
        npse_centre: settings.npse_centre || '',
        bece_centre: settings.bece_centre || '',
        wassce_centre: settings.wassce_centre || '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('school.settings.sierra-leone.update'), { onSuccess: () => toast.success('Settings saved.') });
    };

    return (
        <AppLayout header={<h1 className="text-2xl font-bold">Sierra Leone Education Settings</h1>}>
            <Head title="Sierra Leone Settings" />
            <div className="max-w-4xl mx-auto py-6 px-4 space-y-6">
                <Card>
                    <CardHeader><CardTitle>School: {school.name}</CardTitle></CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <Label>School Level</Label>
                                    <Select value={data.school_level} onValueChange={v => setData('school_level', v)}>
                                        <SelectTrigger><SelectValue placeholder="Select level" /></SelectTrigger>
                                        <SelectContent>
                                            {levels.map(l => <SelectItem key={l} value={l}>{l.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div>
                                    <Label>Grading System</Label>
                                    <Select value={data.grading_system} onValueChange={v => setData('grading_system', v)}>
                                        <SelectTrigger><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="ugce">UGCE (A1-F9)</SelectItem>
                                            <SelectItem value="percentage">Percentage</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div className="grid grid-cols-3 gap-4">
                                <div><Label>NPSE Centre No.</Label><Input value={data.npse_centre} onChange={e => setData('npse_centre', e.target.value)} /></div>
                                <div><Label>BECE Centre No.</Label><Input value={data.bece_centre} onChange={e => setData('bece_centre', e.target.value)} /></div>
                                <div><Label>WASSCE Centre No.</Label><Input value={data.wassce_centre} onChange={e => setData('wassce_centre', e.target.value)} /></div>
                            </div>
                            <Button type="submit" disabled={processing}>Save Settings</Button>
                        </form>
                    </CardContent>
                </Card>
                {departments.length > 0 && (
                    <Card>
                        <CardHeader><CardTitle>Academic Departments</CardTitle></CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-3 gap-2">
                                {departments.map(d => <div key={d.id} className="p-2 border rounded text-sm">{d.name} ({d.code})</div>)}
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}

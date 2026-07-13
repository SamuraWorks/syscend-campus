import AppLayout from '@/Layouts/AppLayout';
import { useForm, router } from '@inertiajs/react';
import { useState, useRef } from 'react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { cn } from '@/lib/utils';
import { toast } from 'sonner';
import {
    Fingerprint, FileText, Phone, Users, GraduationCap, Palette, Globe,
    Save, Upload, Trash2, CheckCircle, Eye, EyeOff,
} from 'lucide-react';

interface SchoolData {
    [key: string]: any;
    name: string; short_name: string | null; motto: string | null;
    logo: string | null; badge: string | null; banner: string | null;
    primary_color: string | null; secondary_color: string | null;
    year_established: string | null; school_level: string | null;
    school_type: string | null; ownership: string | null;
    logo_url: string | null; badge_url: string | null; banner_url: string | null;
    signature_url: string | null; stamp_url: string | null;
    mbsse_registration_number: string | null; emis_code: string | null;
    waec_centre_number: string | null; npse_centre_number: string | null;
    bece_centre_number: string | null; wassce_centre_number: string | null;
    business_registration_number: string | null; tax_identification_number: string | null;
    email: string | null; phone: string | null; whatsapp_number: string | null;
    address: string | null; district_name: string | null; chiefdom: string | null;
    province: string | null; postal_address: string | null; website: string | null;
    gps_latitude: number | null; gps_longitude: number | null;
    proprietor_name: string | null; proprietor_photo: string | null;
    principal_name: string | null; principal_photo: string | null;
    vice_principal_name: string | null; bursar_name: string | null; registrar_name: string | null;
    working_days: string | null; school_opening_time: string | null; school_closing_time: string | null;
    ca_weight: string | null; exam_weight: string | null;
    official_signature: string | null; official_stamp: string | null;
    public_profile_enabled: boolean; about_school: string | null;
    school_mission: string | null; school_vision: string | null;
}

interface Props { school: SchoolData; settings: Record<string, string>; }

const TABS = [
    { id: 'basic',        label: 'Basic Identity',  icon: Fingerprint },
    { id: 'registration', label: 'Registration',     icon: FileText },
    { id: 'contact',      label: 'Contact Info',     icon: Phone },
    { id: 'leadership',   label: 'Leadership',       icon: Users },
    { id: 'academic',     label: 'Academic Config',  icon: GraduationCap },
    { id: 'branding',     label: 'Branding Assets',  icon: Palette },
    { id: 'public',       label: 'Public Profile',   icon: Globe },
] as const;
type TabId = typeof TABS[number]['id'];

const SCHOOL_LEVELS = [
    { value: 'nursery', label: 'Nursery' },
    { value: 'primary', label: 'Primary' },
    { value: 'junior_secondary', label: 'Junior Secondary' },
    { value: 'senior_secondary', label: 'Senior Secondary' },
    { value: 'combined', label: 'Combined School' },
];
const SCHOOL_TYPES = [
    { value: 'government', label: 'Government' },
    { value: 'government_assisted', label: 'Government-Assisted' },
    { value: 'private', label: 'Private' },
    { value: 'community', label: 'Community' },
    { value: 'faith_based', label: 'Faith-Based' },
];
const PROVINCES = ['Northern', 'Southern', 'Eastern', 'Western Area'];

function SuccessBanner({ message }: { message: string }) {
    return (
        <div className="flex items-center gap-2 text-sm text-green-700 bg-green-50 dark:bg-green-950/30 dark:text-green-400 border border-green-200 dark:border-green-800 rounded-lg px-4 py-2">
            <CheckCircle className="w-4 h-4 shrink-0" /> {message}
        </div>
    );
}

function AssetPreview({ url, label, onRemove }: { url: string | null; label: string; onRemove?: () => void }) {
    return (
        <div className="flex items-center gap-4">
            <div className="w-24 h-24 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden bg-slate-50 dark:bg-slate-800">
                {url ? <img src={url} alt={label} className="w-full h-full object-contain p-1" /> : <span className="text-xs text-slate-400 text-center px-2">No {label}</span>}
            </div>
            {onRemove && url && (
                <Button type="button" variant="ghost" size="sm" className="text-red-500 hover:text-red-700" onClick={onRemove}>
                    <Trash2 className="w-3.5 h-3.5 mr-1" /> Remove
                </Button>
            )}
        </div>
    );
}

function FileUpload({ label, accept, description, currentUrl, onFile, onRemove }: {
    label: string; accept?: string; description: string; currentUrl: string | null;
    onFile: (f: File) => void; onRemove?: () => void;
}) {
    const ref = useRef<HTMLInputElement>(null);
    const [preview, setPreview] = useState<string | null>(currentUrl);
    return (
        <div>
            <Label className="block mb-2">{label}</Label>
            <div className="flex items-center gap-4">
                <div className="w-20 h-20 rounded-lg border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden bg-slate-50 dark:bg-slate-800 shrink-0">
                    {preview ? <img src={preview} alt={label} className="w-full h-full object-contain p-1" /> : <span className="text-[10px] text-slate-400 text-center px-1">{label}</span>}
                </div>
                <div>
                    <input ref={ref} type="file" accept={accept ?? 'image/*'} className="hidden" onChange={e => {
                        const f = e.target.files?.[0]; if (f) { onFile(f); setPreview(URL.createObjectURL(f)); }
                    }} />
                    <Button type="button" variant="outline" size="sm" className="gap-2" onClick={() => ref.current?.click()}>
                        <Upload className="w-3.5 h-3.5" /> Upload
                    </Button>
                    <p className="text-xs text-slate-400 mt-1">{description}</p>
                    {preview && onRemove && (
                        <Button type="button" variant="ghost" size="sm" className="text-red-500 mt-1 h-6 px-2" onClick={() => { onRemove(); setPreview(null); }}>
                            <Trash2 className="w-3 h-3 mr-1" /> Remove
                        </Button>
                    )}
                </div>
            </div>
        </div>
    );
}

/* ═══════════════ Basic Identity Tab ═══════════════ */
function BasicTab({ school }: { school: SchoolData }) {
    const form = useForm({
        name: school.name, short_name: school.short_name ?? '', motto: school.motto ?? '',
        primary_color: school.primary_color ?? '#0d6efd', secondary_color: school.secondary_color ?? '#6c757d',
        year_established: school.year_established ?? '', school_level: school.school_level ?? '',
        school_type: school.school_type ?? '', ownership: school.ownership ?? '',
    });

    return (
        <form onSubmit={e => { e.preventDefault(); form.post('/school/school-identity/basic'); }}>
            <Card>
                <CardHeader><CardTitle className="text-base">Basic School Identity</CardTitle><CardDescription>Official name, motto, type, and visual identity colors.</CardDescription></CardHeader>
                <CardContent className="space-y-5">
                    {form.recentlySuccessful && <SuccessBanner message="Identity updated." />}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="sm:col-span-2"><Label>School Name *</Label><Input value={form.data.name} onChange={e => form.setData('name', e.target.value)} />{form.errors.name && <p className="text-xs text-red-500 mt-1">{form.errors.name}</p>}</div>
                        <div><Label>Short Name / Abbreviation</Label><Input value={form.data.short_name} onChange={e => form.setData('short_name', e.target.value)} placeholder="e.g. SCS" /></div>
                        <div><Label>School Motto</Label><Input value={form.data.motto} onChange={e => form.setData('motto', e.target.value)} placeholder="e.g. Knowledge is Power" /></div>
                        <div><Label>School Type</Label><Select value={form.data.school_type} onValueChange={v => form.setData('school_type', v)}><SelectTrigger><SelectValue placeholder="Select type" /></SelectTrigger><SelectContent>{SCHOOL_TYPES.map(t => <SelectItem key={t.value} value={t.value}>{t.label}</SelectItem>)}</SelectContent></Select></div>
                        <div><Label>School Level</Label><Select value={form.data.school_level} onValueChange={v => form.setData('school_level', v)}><SelectTrigger><SelectValue placeholder="Select level" /></SelectTrigger><SelectContent>{SCHOOL_LEVELS.map(l => <SelectItem key={l.value} value={l.value}>{l.label}</SelectItem>)}</SelectContent></Select></div>
                        <div><Label>Year Established</Label><Input type="number" min="1800" max={new Date().getFullYear()} value={form.data.year_established} onChange={e => form.setData('year_established', e.target.value)} placeholder="e.g. 1995" /></div>
                        <div><Label>Ownership</Label><Input value={form.data.ownership} onChange={e => form.setData('ownership', e.target.value)} placeholder="e.g. Community Trust" /></div>
                    </div>
                    <div className="border-t border-slate-100 dark:border-slate-800 pt-4">
                        <h3 className="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">School Colors</h3>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div><Label>Primary Color</Label><div className="flex items-center gap-2"><input type="color" value={form.data.primary_color} onChange={e => form.setData('primary_color', e.target.value)} className="w-10 h-10 rounded border border-slate-200 cursor-pointer p-0.5" /><Input value={form.data.primary_color} onChange={e => form.setData('primary_color', e.target.value)} className="font-mono" /></div></div>
                            <div><Label>Secondary Color</Label><div className="flex items-center gap-2"><input type="color" value={form.data.secondary_color} onChange={e => form.setData('secondary_color', e.target.value)} className="w-10 h-10 rounded border border-slate-200 cursor-pointer p-0.5" /><Input value={form.data.secondary_color} onChange={e => form.setData('secondary_color', e.target.value)} className="font-mono" /></div></div>
                        </div>
                    </div>
                    <div className="flex justify-end"><Button type="submit" disabled={form.processing} className="gap-2"><Save className="w-4 h-4" /> Save Identity</Button></div>
                </CardContent>
            </Card>
        </form>
    );
}

/* ═══════════════ Registration Tab ═══════════════ */
function RegistrationTab({ school }: { school: SchoolData }) {
    const form = useForm({
        mbsse_registration_number: school.mbsse_registration_number ?? '',
        emis_code: school.emis_code ?? '',
        waec_centre_number: school.waec_centre_number ?? '',
        npse_centre_number: school.npse_centre_number ?? '',
        bece_centre_number: school.bece_centre_number ?? '',
        wassce_centre_number: school.wassce_centre_number ?? '',
        business_registration_number: school.business_registration_number ?? '',
        tax_identification_number: school.tax_identification_number ?? '',
    });

    return (
        <form onSubmit={e => { e.preventDefault(); form.post('/school/school-identity/registration'); }}>
            <Card>
                <CardHeader><CardTitle className="text-base">Registration & Official Information</CardTitle><CardDescription>Ministry, examination centre, and business registration numbers.</CardDescription></CardHeader>
                <CardContent className="space-y-5">
                    {form.recentlySuccessful && <SuccessBanner message="Registration details updated." />}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><Label>MBSSE Registration Number</Label><Input value={form.data.mbsse_registration_number} onChange={e => form.setData('mbsse_registration_number', e.target.value)} placeholder="Ministry registration #" /></div>
                        <div><Label>EMIS / National School Code</Label><Input value={form.data.emis_code} onChange={e => form.setData('emis_code', e.target.value)} placeholder="EMIS code" /></div>
                        <div><Label>WAEC Centre Number</Label><Input value={form.data.waec_centre_number} onChange={e => form.setData('waec_centre_number', e.target.value)} placeholder="WAEC centre #" /></div>
                        <div><Label>NPSE Centre Number</Label><Input value={form.data.npse_centre_number} onChange={e => form.setData('npse_centre_number', e.target.value)} placeholder="NPSE centre #" /></div>
                        <div><Label>BECE Centre Number</Label><Input value={form.data.bece_centre_number} onChange={e => form.setData('bece_centre_number', e.target.value)} placeholder="BECE centre #" /></div>
                        <div><Label>WASSCE Centre Number</Label><Input value={form.data.wassce_centre_number} onChange={e => form.setData('wassce_centre_number', e.target.value)} placeholder="WASSCE centre #" /></div>
                        <div><Label>Business Registration Number</Label><Input value={form.data.business_registration_number} onChange={e => form.setData('business_registration_number', e.target.value)} placeholder="Optional" /></div>
                        <div><Label>Tax Identification Number</Label><Input value={form.data.tax_identification_number} onChange={e => form.setData('tax_identification_number', e.target.value)} placeholder="Optional" /></div>
                    </div>
                    <div className="flex justify-end"><Button type="submit" disabled={form.processing} className="gap-2"><Save className="w-4 h-4" /> Save Registration</Button></div>
                </CardContent>
            </Card>
        </form>
    );
}

/* ═══════════════ Contact Tab ═══════════════ */
function ContactTab({ school }: { school: SchoolData }) {
    const form = useForm({
        email: school.email ?? '', phone: school.phone ?? '', whatsapp_number: school.whatsapp_number ?? '',
        address: school.address ?? '', district_name: school.district_name ?? '',
        chiefdom: school.chiefdom ?? '', province: school.province ?? '',
        postal_address: school.postal_address ?? '', website: school.website ?? '',
        gps_latitude: school.gps_latitude ?? '', gps_longitude: school.gps_longitude ?? '',
    });

    return (
        <form onSubmit={e => { e.preventDefault(); form.post('/school/school-identity/contact'); }}>
            <Card>
                <CardHeader><CardTitle className="text-base">Contact Information</CardTitle><CardDescription>Physical address, phone numbers, and online presence.</CardDescription></CardHeader>
                <CardContent className="space-y-5">
                    {form.recentlySuccessful && <SuccessBanner message="Contact info updated." />}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><Label>Email</Label><Input type="email" value={form.data.email} onChange={e => form.setData('email', e.target.value)} /></div>
                        <div><Label>Official Phone</Label><Input value={form.data.phone} onChange={e => form.setData('phone', e.target.value)} /></div>
                        <div><Label>WhatsApp Number</Label><Input value={form.data.whatsapp_number} onChange={e => form.setData('whatsapp_number', e.target.value)} /></div>
                        <div><Label>Website</Label><Input value={form.data.website} onChange={e => form.setData('website', e.target.value)} placeholder="https://..." /></div>
                        <div className="sm:col-span-2"><Label>Physical Address</Label><Textarea rows={2} value={form.data.address} onChange={e => form.setData('address', e.target.value)} /></div>
                        <div><Label>District</Label><Input value={form.data.district_name} onChange={e => form.setData('district_name', e.target.value)} /></div>
                        <div><Label>Chiefdom / City / Town</Label><Input value={form.data.chiefdom} onChange={e => form.setData('chiefdom', e.target.value)} /></div>
                        <div><Label>Province / Region</Label><Select value={form.data.province} onValueChange={v => form.setData('province', v)}><SelectTrigger><SelectValue placeholder="Select province" /></SelectTrigger><SelectContent>{PROVINCES.map(p => <SelectItem key={p} value={p}>{p}</SelectItem>)}</SelectContent></Select></div>
                        <div><Label>Postal Address</Label><Input value={form.data.postal_address} onChange={e => form.setData('postal_address', e.target.value)} placeholder="Optional" /></div>
                        <div><Label>GPS Latitude</Label><Input type="number" step="any" value={form.data.gps_latitude} onChange={e => form.setData('gps_latitude', e.target.value)} placeholder="-8.484..." /></div>
                        <div><Label>GPS Longitude</Label><Input type="number" step="any" value={form.data.gps_longitude} onChange={e => form.setData('gps_longitude', e.target.value)} placeholder="11.310..." /></div>
                    </div>
                    <div className="flex justify-end"><Button type="submit" disabled={form.processing} className="gap-2"><Save className="w-4 h-4" /> Save Contact</Button></div>
                </CardContent>
            </Card>
        </form>
    );
}

/* ═══════════════ Leadership Tab ═══════════════ */
function LeadershipTab({ school }: { school: SchoolData }) {
    const form = useForm<{
        proprietor_name: string; proprietor_photo: File | null;
        principal_name: string; principal_photo: File | null;
        vice_principal_name: string; bursar_name: string; registrar_name: string;
    }>({
        proprietor_name: school.proprietor_name ?? '', proprietor_photo: null,
        principal_name: school.principal_name ?? '', principal_photo: null,
        vice_principal_name: school.vice_principal_name ?? '',
        bursar_name: school.bursar_name ?? '', registrar_name: school.registrar_name ?? '',
    });

    return (
        <form onSubmit={e => { e.preventDefault(); form.post('/school/school-identity/leadership', { forceFormData: true }); }}>
            <Card>
                <CardHeader><CardTitle className="text-base">Leadership Information</CardTitle><CardDescription>School leadership names and profile photos.</CardDescription></CardHeader>
                <CardContent className="space-y-6">
                    {form.recentlySuccessful && <SuccessBanner message="Leadership info updated." />}
                    {/* Proprietor */}
                    <div className="space-y-3">
                        <h4 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Proprietor / Proprietress</h4>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div><Label>Full Name</Label><Input value={form.data.proprietor_name} onChange={e => form.setData('proprietor_name', e.target.value)} /></div>
                            <div>
                                <Label>Profile Photo</Label>
                                <input type="file" accept="image/*" className="hidden" id="proprietor-photo" onChange={e => form.setData('proprietor_photo', e.target.files?.[0] ?? null)} />
                                <div className="flex items-center gap-3 mt-1">
                                    {school.proprietor_photo && <img src={`/storage/${school.proprietor_photo}`} alt="" className="w-10 h-10 rounded-full object-cover" />}
                                    <Button type="button" variant="outline" size="sm" onClick={() => document.getElementById('proprietor-photo')?.click()}>Upload Photo</Button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="border-t border-slate-100 dark:border-slate-800 pt-4 space-y-3">
                        <h4 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Principal / Headteacher</h4>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div><Label>Full Name</Label><Input value={form.data.principal_name} onChange={e => form.setData('principal_name', e.target.value)} /></div>
                            <div>
                                <Label>Profile Photo</Label>
                                <input type="file" accept="image/*" className="hidden" id="principal-photo" onChange={e => form.setData('principal_photo', e.target.files?.[0] ?? null)} />
                                <div className="flex items-center gap-3 mt-1">
                                    {school.principal_photo && <img src={`/storage/${school.principal_photo}`} alt="" className="w-10 h-10 rounded-full object-cover" />}
                                    <Button type="button" variant="outline" size="sm" onClick={() => document.getElementById('principal-photo')?.click()}>Upload Photo</Button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="border-t border-slate-100 dark:border-slate-800 pt-4">
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div><Label>Vice Principal(s)</Label><Input value={form.data.vice_principal_name} onChange={e => form.setData('vice_principal_name', e.target.value)} /></div>
                            <div><Label>Bursar / Accountant</Label><Input value={form.data.bursar_name} onChange={e => form.setData('bursar_name', e.target.value)} /></div>
                            <div><Label>Registrar</Label><Input value={form.data.registrar_name} onChange={e => form.setData('registrar_name', e.target.value)} /></div>
                        </div>
                    </div>
                    <div className="flex justify-end"><Button type="submit" disabled={form.processing} className="gap-2"><Save className="w-4 h-4" /> Save Leadership</Button></div>
                </CardContent>
            </Card>
        </form>
    );
}

/* ═══════════════ Academic Tab ═══════════════ */
function AcademicTab({ school }: { school: SchoolData }) {
    const form = useForm({
        working_days: school.working_days ?? 'Monday-Friday',
        school_opening_time: school.school_opening_time ?? '08:00',
        school_closing_time: school.school_closing_time ?? '15:30',
        ca_weight: school.ca_weight ?? '40',
        exam_weight: school.exam_weight ?? '60',
    });

    return (
        <form onSubmit={e => { e.preventDefault(); form.post('/school/school-identity/academic'); }}>
            <Card>
                <CardHeader><CardTitle className="text-base">Academic Configuration</CardTitle><CardDescription>School hours, working days, and assessment weights.</CardDescription></CardHeader>
                <CardContent className="space-y-5">
                    {form.recentlySuccessful && <SuccessBanner message="Academic config updated." />}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="sm:col-span-2"><Label>Working Days</Label><Input value={form.data.working_days} onChange={e => form.setData('working_days', e.target.value)} placeholder="e.g. Monday-Friday" /></div>
                        <div><Label>School Opening Time</Label><Input type="time" value={form.data.school_opening_time} onChange={e => form.setData('school_opening_time', e.target.value)} /></div>
                        <div><Label>School Closing Time</Label><Input type="time" value={form.data.school_closing_time} onChange={e => form.setData('school_closing_time', e.target.value)} /></div>
                        <div><Label>CA Weight (%)</Label><Input type="number" min="0" max="100" value={form.data.ca_weight} onChange={e => form.setData('ca_weight', e.target.value)} /></div>
                        <div><Label>Exam Weight (%)</Label><Input type="number" min="0" max="100" value={form.data.exam_weight} onChange={e => form.setData('exam_weight', e.target.value)} /></div>
                    </div>
                    <div className="flex justify-end"><Button type="submit" disabled={form.processing} className="gap-2"><Save className="w-4 h-4" /> Save Academic Config</Button></div>
                </CardContent>
            </Card>
        </form>
    );
}

/* ═══════════════ Branding Tab ═══════════════ */
function BrandingTab({ school }: { school: SchoolData }) {
    const form = useForm<{
        logo: File | null; badge: File | null; banner: File | null;
        official_signature: File | null; official_stamp: File | null;
    }>({ logo: null, badge: null, banner: null, official_signature: null, official_stamp: null });

    const uploadField = (field: string, file: File) => {
        form.setData(field as any, file);
    };

    const removeAsset = (field: string) => {
        router.delete('/school/school-identity/asset', { data: { field }, preserveScroll: true });
    };

    return (
        <form onSubmit={e => { e.preventDefault(); form.post('/school/school-identity/branding', { forceFormData: true }); }}>
            <Card>
                <CardHeader><CardTitle className="text-base">Branding Assets</CardTitle><CardDescription>Upload logos, crests, signatures, and stamps. These appear on all official documents.</CardDescription></CardHeader>
                <CardContent className="space-y-6">
                    {form.recentlySuccessful && <SuccessBanner message="Branding assets updated." />}
                    <FileUpload label="School Logo" currentUrl={school.logo_url} description="PNG/SVG, max 2MB. Recommended 200x200px." onFile={f => uploadField('logo', f)} onRemove={() => removeAsset('logo')} />
                    <FileUpload label="School Badge / Crest" currentUrl={school.badge_url} description="PNG/SVG, max 2MB." onFile={f => uploadField('badge', f)} onRemove={() => removeAsset('badge')} />
                    <FileUpload label="School Banner" currentUrl={school.banner_url} description="JPG/PNG, max 4MB. Recommended 1200x400px." onFile={f => uploadField('banner', f)} onRemove={() => removeAsset('banner')} />
                    <div className="border-t border-slate-100 dark:border-slate-800 pt-4">
                        <h4 className="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Official Marks</h4>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <FileUpload label="Official Signature" currentUrl={school.signature_url} description="PNG with transparent bg, max 1MB." onFile={f => uploadField('official_signature', f)} onRemove={() => removeAsset('official_signature')} />
                            <FileUpload label="Official School Stamp" currentUrl={school.stamp_url} description="PNG with transparent bg, max 1MB." onFile={f => uploadField('official_stamp', f)} onRemove={() => removeAsset('official_stamp')} />
                        </div>
                    </div>
                    <div className="flex justify-end"><Button type="submit" disabled={form.processing} className="gap-2"><Save className="w-4 h-4" /> Save Branding</Button></div>
                </CardContent>
            </Card>
        </form>
    );
}

/* ═══════════════ Public Profile Tab ═══════════════ */
function PublicProfileTab({ school }: { school: SchoolData }) {
    const form = useForm({
        public_profile_enabled: school.public_profile_enabled ?? false,
        about_school: school.about_school ?? '',
        school_mission: school.school_mission ?? '',
        school_vision: school.school_vision ?? '',
    });

    return (
        <form onSubmit={e => { e.preventDefault(); form.post('/school/school-identity/public-profile'); }}>
            <Card>
                <CardHeader><CardTitle className="text-base">Public School Profile</CardTitle><CardDescription>Control what visitors see on your public-facing school profile page.</CardDescription></CardHeader>
                <CardContent className="space-y-5">
                    {form.recentlySuccessful && <SuccessBanner message="Public profile updated." />}
                    <div className="flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 p-4">
                        <div>
                            <p className="text-sm font-medium">Enable Public Profile</p>
                            <p className="text-xs text-slate-500 mt-0.5">Allow anyone to view your school's public profile page.</p>
                        </div>
                        <Switch checked={form.data.public_profile_enabled} onCheckedChange={v => form.setData('public_profile_enabled', v)} />
                    </div>
                    <div><Label>About the School</Label><Textarea rows={4} value={form.data.about_school} onChange={e => form.setData('about_school', e.target.value)} placeholder="Brief description of your school..." /></div>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><Label>Mission Statement</Label><Textarea rows={3} value={form.data.school_mission} onChange={e => form.setData('school_mission', e.target.value)} placeholder="Our mission is to..." /></div>
                        <div><Label>Vision Statement</Label><Textarea rows={3} value={form.data.school_vision} onChange={e => form.setData('school_vision', e.target.value)} placeholder="Our vision is to..." /></div>
                    </div>
                    <div className="flex justify-end"><Button type="submit" disabled={form.processing} className="gap-2"><Save className="w-4 h-4" /> Save Public Profile</Button></div>
                </CardContent>
            </Card>
        </form>
    );
}

/* ═══════════════ Main Page ═══════════════ */
export default function SchoolIdentityIndex({ school, settings }: Props) {
    const [activeTab, setActiveTab] = useState<TabId>('basic');

    return (
        <AppLayout title="School Identity">
            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-foreground">School Identity & Branding</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">Manage your school's official identity, registration, and branding</p>
                </div>

                <div className="flex gap-6">
                    <nav className="w-48 shrink-0">
                        <ul className="space-y-0.5">
                            {TABS.map(t => (
                                <li key={t.id}>
                                    <button onClick={() => setActiveTab(t.id)} className={cn(
                                        'w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors text-left',
                                        activeTab === t.id ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground',
                                    )}>
                                        <t.icon className="w-4 h-4 shrink-0" />
                                        {t.label}
                                    </button>
                                </li>
                            ))}
                        </ul>
                    </nav>

                    <div className="flex-1 min-w-0">
                        {activeTab === 'basic'        && <BasicTab school={school} />}
                        {activeTab === 'registration' && <RegistrationTab school={school} />}
                        {activeTab === 'contact'      && <ContactTab school={school} />}
                        {activeTab === 'leadership'   && <LeadershipTab school={school} />}
                        {activeTab === 'academic'     && <AcademicTab school={school} />}
                        {activeTab === 'branding'     && <BrandingTab school={school} />}
                        {activeTab === 'public'       && <PublicProfileTab school={school} />}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { cn } from '@/lib/utils';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Input } from '@/components/ui/input';
import { MessageSquare, ChevronRight, Send, User } from 'lucide-react';

interface Contact { id: number; name: string; role: string; last_message: string | null; last_time: string | null; unread_count: number; }
interface Thread { id: number; contact_name: string; contact_role: string; messages: MessageItem[]; }
interface MessageItem { id: number; sender: string; content: string; time: string; is_mine: boolean; }
interface Props { linked: boolean; contacts: Contact[]; activeThread: Thread | null; }

export default function Messages({ linked, contacts, activeThread }: Props) {
    const [selectedContact, setSelectedContact] = useState<number | null>(activeThread?.id ?? null);
    const { data, setData, post, processing, reset } = useForm({ content: '' });

    if (!linked) {
        return (
            <AppLayout title="Messages">
                <div className="flex flex-col items-center justify-center py-24 text-center">
                    <MessageSquare className="w-14 h-14 text-slate-300 mb-4" />
                    <h2 className="text-lg font-semibold text-slate-600 dark:text-slate-400">Account not linked</h2>
                </div>
            </AppLayout>
        );
    }

    const handleSend = () => {
        if (!data.content.trim() || !selectedContact) return;
        post(`/school/principal/messages/${selectedContact}`, {
            onSuccess: () => { reset(); },
        });
    };

    return (
        <AppLayout title="Messages">
            <div className="space-y-6">
                <div className="flex items-center gap-2 text-xs text-slate-500">
                    <Link href="/school/principal/dashboard" className="hover:text-slate-700 dark:hover:text-slate-300">Principal</Link>
                    <ChevronRight className="w-3 h-3" />
                    <span className="text-slate-700 dark:text-slate-300 font-medium">Messages</span>
                </div>

                <div>
                    <h1 className="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <MessageSquare className="w-5 h-5 text-blue-500" /> Messages
                    </h1>
                    <p className="text-sm text-slate-500 mt-0.5">Internal messaging</p>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4 min-h-[500px]">
                    <Card className="lg:col-span-1">
                        <div className="p-4 border-b border-slate-100 dark:border-slate-800">
                            <h2 className="text-sm font-semibold text-slate-700 dark:text-slate-300">Contacts</h2>
                        </div>
                        <CardContent className="p-0">
                            {contacts.length === 0 ? (
                                <div className="py-12 text-center"><p className="text-sm text-slate-400">No contacts.</p></div>
                            ) : (
                                <div className="divide-y divide-slate-100 dark:divide-slate-800">
                                    {contacts.map(c => (
                                        <button key={c.id} onClick={() => setSelectedContact(c.id)}
                                            className={cn('w-full text-left px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors', selectedContact === c.id && 'bg-indigo-50 dark:bg-indigo-950/20 border-r-2 border-indigo-500')}>
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                                    <User className="w-4 h-4 text-slate-500" />
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <div className="flex items-center justify-between">
                                                        <p className="text-sm font-medium text-slate-800 dark:text-slate-200 truncate">{c.name}</p>
                                                        {c.unread_count > 0 && <Badge className="ml-1 h-5 min-w-5 text-[10px] px-1">{c.unread_count}</Badge>}
                                                    </div>
                                                    <p className="text-xs text-slate-400 capitalize">{c.role}</p>
                                                    {c.last_message && <p className="text-xs text-slate-400 truncate mt-0.5">{c.last_message}</p>}
                                                </div>
                                            </div>
                                        </button>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card className="lg:col-span-2 flex flex-col">
                        {!selectedContact ? (
                            <div className="flex-1 flex items-center justify-center">
                                <div className="text-center">
                                    <MessageSquare className="w-10 h-10 mx-auto mb-3 text-slate-300" />
                                    <p className="text-sm text-slate-400">Select a contact to start chatting</p>
                                </div>
                            </div>
                        ) : activeThread && selectedContact === activeThread.id ? (
                            <>
                                <div className="p-4 border-b border-slate-100 dark:border-slate-800">
                                    <h3 className="text-sm font-semibold text-slate-800 dark:text-slate-200">{activeThread.contact_name}</h3>
                                    <p className="text-xs text-slate-400 capitalize">{activeThread.contact_role}</p>
                                </div>
                                <div className="flex-1 overflow-y-auto p-4 space-y-3 max-h-[400px]">
                                    {activeThread.messages.length === 0 ? (
                                        <p className="text-center text-sm text-slate-400 py-8">No messages yet. Start the conversation.</p>
                                    ) : (
                                        activeThread.messages.map(m => (
                                            <div key={m.id} className={cn('flex', m.is_mine ? 'justify-end' : 'justify-start')}>
                                                <div className={cn('max-w-[75%] rounded-xl px-3 py-2', m.is_mine ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200')}>
                                                    <p className="text-sm">{m.content}</p>
                                                    <p className={cn('text-[10px] mt-1', m.is_mine ? 'text-white/60' : 'text-slate-400')}>{m.time}</p>
                                                </div>
                                            </div>
                                        ))
                                    )}
                                </div>
                                <div className="p-4 border-t border-slate-100 dark:border-slate-800">
                                    <div className="flex items-end gap-2">
                                        <Textarea placeholder="Type a message…" className="min-h-[40px] max-h-[120px] resize-none" value={data.content} onChange={e => setData('content', e.target.value)} onKeyDown={e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSend(); } }} />
                                        <Button size="icon" className="shrink-0 h-10 w-10" onClick={handleSend} disabled={!data.content.trim() || processing}>
                                            <Send className="w-4 h-4" />
                                        </Button>
                                    </div>
                                </div>
                            </>
                        ) : (
                            <div className="flex-1 flex items-center justify-center">
                                <p className="text-sm text-slate-400">Loading thread…</p>
                            </div>
                        )}
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}

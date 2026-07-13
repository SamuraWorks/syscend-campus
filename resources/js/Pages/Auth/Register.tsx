import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { router, usePage, Head, Link } from '@inertiajs/react';
import { toast } from 'sonner';
import { useEffect } from 'react';

import AuthLayout from '@/Layouts/AuthLayout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { PageProps } from '@/Types';

const registerSchema = z.object({
    name: z.string().min(2, 'Name must be at least 2 characters'),
    email: z.string().email('Please enter a valid email address'),
    phone: z.string().optional(),
    password: z.string().min(8, 'Password must be at least 8 characters'),
    password_confirmation: z.string(),
}).refine((data) => data.password === data.password_confirmation, {
    message: 'Passwords do not match',
    path: ['password_confirmation'],
});

type RegisterFormData = z.infer<typeof registerSchema>;

interface RegisterProps extends PageProps {
    errors?: Record<string, string>;
}

export default function Register() {
    const { errors: serverErrors } = usePage<RegisterProps>().props;

    const {
        register,
        handleSubmit,
        setError,
        formState: { errors, isSubmitting },
    } = useForm<RegisterFormData>({
        resolver: zodResolver(registerSchema),
        defaultValues: { name: '', email: '', phone: '', password: '', password_confirmation: '' },
    });

    useEffect(() => {
        if (serverErrors) {
            Object.entries(serverErrors).forEach(([field, message]) => {
                setError(field as keyof RegisterFormData, { message });
            });
        }
    }, [serverErrors, setError]);

    const onSubmit = (data: RegisterFormData) => {
        router.post('/register', data, {
            onError: (errs) => {
                Object.entries(errs).forEach(([field, message]) => {
                    setError(field as keyof RegisterFormData, { message });
                });
                if (errs.message) toast.error(errs.message);
            },
        });
    };

    return (
        <AuthLayout>
            <Head title="Create Account" />

            <div className="w-full max-w-md">
                {/* Logo / Branding — tap to go home */}
                <Link href="/" className="block text-center mb-8">
                    <img
                        src="/images/logo.jpeg"
                        alt="Syscend Campus"
                        className="inline-block w-16 h-16 rounded-2xl object-cover mb-4 shadow-lg"
                    />
                    <h1 className="text-2xl font-bold text-foreground tracking-tight">
                        Create your account
                    </h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Join Syscend Campus today
                    </p>
                </Link>

                <Card className="shadow-xl border-0 bg-card">
                    <CardHeader className="space-y-1 pb-4">
                        <CardTitle className="text-xl font-semibold text-foreground">
                            Get started
                        </CardTitle>
                        <CardDescription className="text-muted-foreground">
                            Fill in your details to create an account
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4" noValidate>
                            {/* Name */}
                            <div className="space-y-1.5">
                                <Label htmlFor="name" className="text-sm font-medium text-foreground">
                                    Full name
                                </Label>
                                <Input
                                    id="name"
                                    type="text"
                                    autoComplete="name"
                                    autoFocus
                                    placeholder="John Doe"
                                    className="h-10"
                                    {...register('name')}
                                />
                                {errors.name && (
                                    <p className="text-xs text-red-500 mt-1">{errors.name.message}</p>
                                )}
                            </div>

                            {/* Email */}
                            <div className="space-y-1.5">
                                <Label htmlFor="email" className="text-sm font-medium text-foreground">
                                    Email address
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    autoComplete="email"
                                    placeholder="you@example.com"
                                    className="h-10"
                                    {...register('email')}
                                />
                                {errors.email && (
                                    <p className="text-xs text-red-500 mt-1">{errors.email.message}</p>
                                )}
                            </div>

                            {/* Phone */}
                            <div className="space-y-1.5">
                                <Label htmlFor="phone" className="text-sm font-medium text-foreground">
                                    Phone number <span className="text-muted-foreground font-normal">(optional)</span>
                                </Label>
                                <Input
                                    id="phone"
                                    type="tel"
                                    autoComplete="tel"
                                    placeholder="+232 XX XXX XXXX"
                                    className="h-10"
                                    {...register('phone')}
                                />
                                {errors.phone && (
                                    <p className="text-xs text-red-500 mt-1">{errors.phone.message}</p>
                                )}
                            </div>

                            {/* Password */}
                            <div className="space-y-1.5">
                                <Label htmlFor="password" className="text-sm font-medium text-foreground">
                                    Password
                                </Label>
                                <Input
                                    id="password"
                                    type="password"
                                    autoComplete="new-password"
                                    placeholder="••••••••"
                                    className="h-10"
                                    {...register('password')}
                                />
                                {errors.password && (
                                    <p className="text-xs text-red-500 mt-1">{errors.password.message}</p>
                                )}
                            </div>

                            {/* Confirm Password */}
                            <div className="space-y-1.5">
                                <Label htmlFor="password_confirmation" className="text-sm font-medium text-foreground">
                                    Confirm password
                                </Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    autoComplete="new-password"
                                    placeholder="••••••••"
                                    className="h-10"
                                    {...register('password_confirmation')}
                                />
                                {errors.password_confirmation && (
                                    <p className="text-xs text-red-500 mt-1">{errors.password_confirmation.message}</p>
                                )}
                            </div>

                            {/* Submit */}
                            <Button
                                type="submit"
                                className="w-full h-10 bg-primary hover:bg-primary/90 text-primary-foreground font-medium transition-colors mt-2"
                                disabled={isSubmitting}
                            >
                                {isSubmitting ? (
                                    <span className="flex items-center gap-2">
                                        <svg className="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                        </svg>
                                        Creating account…
                                    </span>
                                ) : (
                                    'Create account'
                                )}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <p className="text-center text-xs text-muted-foreground mt-6">
                    &copy; {new Date().getFullYear()} Syscend Campus. All rights reserved.
                </p>

                <p className="text-center text-sm text-muted-foreground mt-4">
                    Already have an account?{' '}
                    <Link href="/login" className="font-medium text-primary hover:text-primary/80 transition-colors">
                        Sign in
                    </Link>
                </p>
            </div>
        </AuthLayout>
    );
}

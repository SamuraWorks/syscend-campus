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
import { Checkbox } from '@/components/ui/checkbox';
import type { PageProps } from '@/Types';

const loginSchema = z.object({
    email: z.string().email('Please enter a valid email address'),
    password: z.string().min(1, 'Password is required'),
    remember: z.boolean().optional(),
});

type LoginFormData = z.infer<typeof loginSchema>;

interface LoginProps extends PageProps {}

export default function Login() {
    const { flash, errors: serverErrors } = usePage<LoginProps>().props;

    const {
        register,
        handleSubmit,
        setValue,
        watch,
        setError,
        formState: { errors, isSubmitting },
    } = useForm<LoginFormData>({
        resolver: zodResolver(loginSchema),
        defaultValues: { email: '', password: '', remember: false },
    });

    useEffect(() => {
        if (flash?.error) toast.error(flash.error);
        if (flash?.success) toast.success(flash.success);
    }, [flash]);

    useEffect(() => {
        if (serverErrors?.email) setError('email', { message: serverErrors.email });
        if (serverErrors?.password) setError('password', { message: serverErrors.password });
    }, [serverErrors, setError]);

    const onSubmit = (data: LoginFormData) => {
        router.post('/login', data, {
            onError: (errs) => {
                if (errs.email) setError('email', { message: errs.email });
                if (errs.password) setError('password', { message: errs.password });
                if (errs.message) toast.error(errs.message);
            },
        });
    };

    const remember = watch('remember');

    return (
        <AuthLayout>
            <Head title="Login" />

            <div className="w-full max-w-md">
                {/* Logo / Branding — tap to go home */}
                <Link href="/" className="block text-center mb-8">
                    <img
                        src="/images/logo.jpeg"
                        alt="Syscend Campus"
                        className="inline-block w-16 h-16 rounded-2xl object-cover mb-4 shadow-lg"
                    />
                    <h1 className="text-2xl font-bold text-foreground tracking-tight">
                        Syscend Campus
                    </h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Campus Management System
                    </p>
                </Link>

                <Card className="shadow-xl border-0 bg-card">
                    <CardHeader className="space-y-1 pb-4">
                        <CardTitle className="text-xl font-semibold text-foreground">
                            Welcome back
                        </CardTitle>
                        <CardDescription className="text-muted-foreground">
                            Sign in to your account to continue
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4" noValidate>
                            {/* Email */}
                            <div className="space-y-1.5">
                                <Label htmlFor="email" className="text-sm font-medium text-foreground">
                                    Email address
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    autoComplete="email"
                                    autoFocus
                                    placeholder="admin@school.edu"
                                    className="h-10"
                                    {...register('email')}
                                />
                                {errors.email && (
                                    <p className="text-xs text-red-500 mt-1">{errors.email.message}</p>
                                )}
                            </div>

                            {/* Password */}
                            <div className="space-y-1.5">
                                <div className="flex items-center justify-between">
                                    <Label htmlFor="password" className="text-sm font-medium text-foreground">
                                        Password
                                    </Label>
                                    <a
                                        href="/forgot-password"
                                        className="text-xs text-primary hover:text-primary/80 font-medium transition-colors"
                                    >
                                        Forgot password?
                                    </a>
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    autoComplete="current-password"
                                    placeholder="••••••••"
                                    className="h-10"
                                    {...register('password')}
                                />
                                {errors.password && (
                                    <p className="text-xs text-red-500 mt-1">{errors.password.message}</p>
                                )}
                            </div>

                            {/* Remember me */}
                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="remember"
                                    checked={remember ?? false}
                                    onCheckedChange={(checked) =>
                                        setValue('remember', checked === true)
                                    }
                                />
                                <Label
                                    htmlFor="remember"
                                    className="text-sm text-muted-foreground cursor-pointer select-none"
                                >
                                    Remember me for 30 days
                                </Label>
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
                                        Signing in…
                                    </span>
                                ) : (
                                    'Sign in'
                                )}
                            </Button>
                        </form>
                    </CardContent>
                </Card>

                <p className="text-center text-xs text-muted-foreground mt-6">
                    &copy; {new Date().getFullYear()} Syscend Campus. All rights reserved.
                </p>

                <p className="text-center text-sm text-muted-foreground mt-4">
                    Don&apos;t have an account?{' '}
                    <Link href="/register" className="font-medium text-primary hover:text-primary/80 transition-colors">
                        Sign up
                    </Link>
                </p>
            </div>
        </AuthLayout>
    );
}

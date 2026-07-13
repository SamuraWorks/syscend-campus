import { Head } from '@inertiajs/react';
import SiteHeader from '@/components/landing/SiteHeader';
import SiteFooter from '@/components/landing/SiteFooter';
import Hero from '@/components/landing/Hero';
import Stats from '@/components/landing/Stats';
import Modules from '@/components/landing/Modules';
import AcademicStructure from '@/components/landing/AcademicStructure';
import Exams from '@/components/landing/Exams';
import MultiSchool from '@/components/landing/MultiSchool';
import Roles from '@/components/landing/Roles';
import VisionMission from '@/components/landing/VisionMission';
import MinistryPortal from '@/components/landing/MinistryPortal';
import CTA from '@/components/landing/CTA';

export default function Homepage() {
    return (
        <>
            <Head title="Syscend Campus — School Management Platform for Sierra Leone" />
            <SiteHeader />
            <main>
                <Hero />
                <Stats />
                <Modules />
                <AcademicStructure />
                <Exams />
                <MultiSchool />
                <Roles />
                <VisionMission />
                <MinistryPortal />
                <CTA />
            </main>
            <SiteFooter />
        </>
    );
}

const { PrismaClient } = require('@prisma/client');
const bcrypt = require('bcryptjs');
const prisma = new PrismaClient();

async function main() {
    console.log('Start seeding ...');

    // 1. Create Employer
    const hashedPassword = await bcrypt.hash('123456', 10);

    // Using upsert to avoid errors if exists
    const employer = await prisma.user.upsert({
        where: { email: 'employer@matcha.com' },
        update: {},
        create: {
            email: 'employer@matcha.com',
            password: hashedPassword,
            name: 'Tech Recruiter',
            role: 'EMPLOYER',
            companyName: 'TechStart Ltd.',
            companyLogo: 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80',
            companyBio: 'Leading tech company.',
            onboardingComplete: true
        }
    });

    console.log(`Employer ensured: ${employer.id}`);

    // 2. Create Jobs linked to Employer
    // We delete existing jobs first to avoid duplicates if re-running (optional but cleaner for dev)
    // await prisma.job.deleteMany({ where: { employerId: employer.id } });

    const jobsData = [
        {
            title: "Frontend Developer",
            description: "דרוש/ה מפתח/ת Frontend תותח/ית עם ניסיון ב-React.",
            location: "תל אביב",
            salaryMin: 18000,
            salaryMax: 24000,
            salaryRange: "18k-24k",
            type: "Full-time",
            workModel: "hybrid",
            image: "https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80",
            employerId: employer.id,
            isActive: true
        },
        {
            title: "UI/UX Designer",
            description: "מחפשים מעצב/ת מוכשר/ת שחי/ה ונושמ/ת עיצוב.",
            location: "רמת גן",
            salaryMin: 15000,
            salaryMax: 20000,
            salaryRange: "15k-20k",
            type: "Full-time",
            workModel: "office",
            image: "https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=400&q=80",
            employerId: employer.id,
            isActive: true
        },
        {
            title: "Marketing Manager",
            description: "ניהול קמפיינים בדיגיטל, עבודה מול משפיענים.",
            location: "מהבית",
            salaryMin: 16000,
            salaryMax: 22000,
            salaryRange: "16k-22k",
            type: "Part-time",
            workModel: "remote",
            image: "https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&w=400&q=80",
            employerId: employer.id,
            isActive: true
        }
    ];

    for (const job of jobsData) {
        // Create only if similar doesn't exist to prevent flooding on multiple runs
        // For simplicity in this script, we just create. 
        // Real logic might check "where: { title: ... }"
        await prisma.job.create({ data: job });
    }

    console.log(`Seeded ${jobsData.length} jobs.`);
}

main()
    .catch((e) => {
        console.error(e);
        process.exit(1);
    })
    .finally(async () => {
        await prisma.$disconnect();
    });

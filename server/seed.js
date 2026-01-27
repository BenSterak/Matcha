const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function main() {
    console.log('Start seeding ...');

    // Create Jobs
    const jobs = await prisma.job.createMany({
        data: [
            {
                title: "Frontend Developer",
                company: "TechStart Ltd.",
                description: "דרוש/ה מפתח/ת Frontend תותח/ית עם ניסיון ב-React.",
                location: "תל אביב",
                salaryRange: "18k-24k",
                type: "היברידי",
                image: "https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80"
            },
            {
                title: "UI/UX Designer",
                company: "Creative Studio",
                description: "מחפשים מעצב/ת מוכשר/ת שחי/ה ונושמ/ת עיצוב.",
                location: "רמת גן",
                salaryRange: "15k-20k",
                type: "משרד",
                image: "https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=400&q=80"
            }
        ]
    });

    console.log(`Seeded ${jobs.count} jobs.`);
}

main()
    .catch((e) => {
        console.error(e);
        process.exit(1);
    })
    .finally(async () => {
        await prisma.$disconnect();
    });

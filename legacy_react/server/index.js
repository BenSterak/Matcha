const express = require('express');
const cors = require('cors');
const bcrypt = require('bcryptjs');
const { PrismaClient } = require('@prisma/client');
const path = require('path');
const { authenticate, requireEmployer, requireCandidate, generateToken } = require('./middleware/auth');

const prisma = new PrismaClient();
const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json({ limit: '10mb' }));

// Serve static files from the React app
app.use(express.static(path.join(__dirname, '../dist')));

// ============================================
// AUTH ROUTES
// ============================================

// Register new user
app.post('/api/auth/register', async (req, res) => {
  const { email, password, role = 'CANDIDATE' } = req.body;

  try {
    // Validate input
    if (!email || !password) {
      return res.status(400).json({ error: 'Email and password are required' });
    }

    if (password.length < 6) {
      return res.status(400).json({ error: 'Password must be at least 6 characters' });
    }

    // Check if user already exists
    const existingUser = await prisma.user.findUnique({
      where: { email: email.toLowerCase() },
    });

    if (existingUser) {
      return res.status(400).json({ error: 'User with this email already exists' });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Create user
    const user = await prisma.user.create({
      data: {
        email: email.toLowerCase(),
        password: hashedPassword,
        role: role === 'EMPLOYER' ? 'EMPLOYER' : 'CANDIDATE',
      },
      select: {
        id: true,
        email: true,
        name: true,
        role: true,
        onboardingComplete: true,
        createdAt: true,
      },
    });

    // Generate token
    const token = generateToken(user.id);

    res.status(201).json({ user, token });
  } catch (error) {
    console.error('Registration error:', error);
    res.status(500).json({ error: 'Failed to register user' });
  }
});

// Login user
app.post('/api/auth/login', async (req, res) => {
  const { email, password } = req.body;

  try {
    if (!email || !password) {
      return res.status(400).json({ error: 'Email and password are required' });
    }

    // Find user
    const user = await prisma.user.findUnique({
      where: { email: email.toLowerCase() },
    });

    if (!user) {
      return res.status(401).json({ error: 'Invalid email or password' });
    }

    // Verify password
    const isValidPassword = await bcrypt.compare(password, user.password);

    if (!isValidPassword) {
      return res.status(401).json({ error: 'Invalid email or password' });
    }

    // Generate token
    const token = generateToken(user.id);

    // Return user without password
    const { password: _, ...userWithoutPassword } = user;

    res.json({ user: userWithoutPassword, token });
  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({ error: 'Failed to login' });
  }
});

// Get current user
app.get('/api/auth/me', authenticate, async (req, res) => {
  res.json(req.user);
});

// Update user profile
app.put('/api/auth/profile', authenticate, async (req, res) => {
  const { name, bio, field, salary, workModel, photo, companyName, companyLogo, companyBio, industry, companySize, onboardingComplete } = req.body;

  try {
    const updateData = {};

    // Common fields
    if (name !== undefined) updateData.name = name;
    if (bio !== undefined) updateData.bio = bio;
    if (photo !== undefined) updateData.photo = photo;
    if (onboardingComplete !== undefined) updateData.onboardingComplete = onboardingComplete;

    // Candidate-specific fields
    if (req.user.role === 'CANDIDATE') {
      if (field !== undefined) updateData.field = field;
      if (salary !== undefined) updateData.salary = parseInt(salary) || null;
      if (workModel !== undefined) updateData.workModel = workModel;
    }

    // Employer-specific fields
    if (req.user.role === 'EMPLOYER') {
      if (companyName !== undefined) updateData.companyName = companyName;
      if (companyLogo !== undefined) updateData.companyLogo = companyLogo;
      if (companyBio !== undefined) updateData.companyBio = companyBio;
      if (industry !== undefined) updateData.industry = industry;
      if (companySize !== undefined) updateData.companySize = companySize;
    }

    const updatedUser = await prisma.user.update({
      where: { id: req.user.id },
      data: updateData,
      select: {
        id: true,
        email: true,
        name: true,
        role: true,
        bio: true,
        field: true,
        salary: true,
        workModel: true,
        photo: true,
        companyName: true,
        companyLogo: true,
        companyBio: true,
        industry: true,
        companySize: true,
        onboardingComplete: true,
        createdAt: true,
      },
    });

    res.json(updatedUser);
  } catch (error) {
    console.error('Profile update error:', error);
    res.status(500).json({ error: 'Failed to update profile' });
  }
});

// ============================================
// JOBS ROUTES
// ============================================

// Get all active jobs (for candidates)
app.get('/api/jobs', async (req, res) => {
  try {
    const jobs = await prisma.job.findMany({
      where: { isActive: true },
      include: {
        employer: {
          select: {
            id: true,
            companyName: true,
            companyLogo: true,
            industry: true,
          },
        },
      },
      orderBy: { createdAt: 'desc' },
    });

    // Transform jobs to include company info at top level
    const transformedJobs = jobs.map(job => ({
      ...job,
      company: job.employer?.companyName || 'Unknown Company',
      companyLogo: job.employer?.companyLogo,
    }));

    res.json(transformedJobs);
  } catch (error) {
    console.error('Error fetching jobs:', error);
    res.status(500).json({ error: 'Failed to fetch jobs' });
  }
});

// Get single job
app.get('/api/jobs/:id', async (req, res) => {
  try {
    const job = await prisma.job.findUnique({
      where: { id: parseInt(req.params.id) },
      include: {
        employer: {
          select: {
            id: true,
            companyName: true,
            companyLogo: true,
            companyBio: true,
            industry: true,
            companySize: true,
          },
        },
      },
    });

    if (!job) {
      return res.status(404).json({ error: 'Job not found' });
    }

    res.json(job);
  } catch (error) {
    console.error('Error fetching job:', error);
    res.status(500).json({ error: 'Failed to fetch job' });
  }
});

// Create job (employer only)
app.post('/api/jobs', authenticate, requireEmployer, async (req, res) => {
  const { title, description, location, salaryMin, salaryMax, type, workModel, tags, image } = req.body;

  try {
    if (!title || !description || !location || !type) {
      return res.status(400).json({ error: 'Missing required fields' });
    }

    // Generate salary range string
    let salaryRange = null;
    if (salaryMin && salaryMax) {
      salaryRange = `${Math.round(salaryMin / 1000)}k-${Math.round(salaryMax / 1000)}k`;
    } else if (salaryMin) {
      salaryRange = `${Math.round(salaryMin / 1000)}k+`;
    }

    const job = await prisma.job.create({
      data: {
        title,
        description,
        location,
        salaryMin: salaryMin ? parseInt(salaryMin) : null,
        salaryMax: salaryMax ? parseInt(salaryMax) : null,
        salaryRange,
        type,
        workModel,
        tags,
        image,
        employerId: req.user.id,
      },
    });

    res.status(201).json(job);
  } catch (error) {
    console.error('Error creating job:', error);
    res.status(500).json({ error: 'Failed to create job' });
  }
});

// Get employer's jobs
app.get('/api/employer/jobs', authenticate, requireEmployer, async (req, res) => {
  try {
    const jobs = await prisma.job.findMany({
      where: { employerId: req.user.id },
      include: {
        _count: {
          select: {
            matches: {
              where: { candidateStatus: 'LIKED' },
            },
          },
        },
      },
      orderBy: { createdAt: 'desc' },
    });

    res.json(jobs);
  } catch (error) {
    console.error('Error fetching employer jobs:', error);
    res.status(500).json({ error: 'Failed to fetch jobs' });
  }
});

// Update job (employer only)
app.put('/api/jobs/:id', authenticate, requireEmployer, async (req, res) => {
  const jobId = parseInt(req.params.id);
  const { title, description, location, salaryMin, salaryMax, type, workModel, tags, image, isActive } = req.body;

  try {
    // Verify ownership
    const job = await prisma.job.findFirst({
      where: { id: jobId, employerId: req.user.id },
    });

    if (!job) {
      return res.status(404).json({ error: 'Job not found or access denied' });
    }

    const updateData = {};
    if (title !== undefined) updateData.title = title;
    if (description !== undefined) updateData.description = description;
    if (location !== undefined) updateData.location = location;
    if (salaryMin !== undefined) updateData.salaryMin = parseInt(salaryMin) || null;
    if (salaryMax !== undefined) updateData.salaryMax = parseInt(salaryMax) || null;
    if (type !== undefined) updateData.type = type;
    if (workModel !== undefined) updateData.workModel = workModel;
    if (tags !== undefined) updateData.tags = tags;
    if (image !== undefined) updateData.image = image;
    if (isActive !== undefined) updateData.isActive = isActive;

    // Update salary range
    if (updateData.salaryMin || updateData.salaryMax) {
      const min = updateData.salaryMin || job.salaryMin;
      const max = updateData.salaryMax || job.salaryMax;
      if (min && max) {
        updateData.salaryRange = `${Math.round(min / 1000)}k-${Math.round(max / 1000)}k`;
      }
    }

    const updatedJob = await prisma.job.update({
      where: { id: jobId },
      data: updateData,
    });

    res.json(updatedJob);
  } catch (error) {
    console.error('Error updating job:', error);
    res.status(500).json({ error: 'Failed to update job' });
  }
});

// Delete job (employer only)
app.delete('/api/jobs/:id', authenticate, requireEmployer, async (req, res) => {
  const jobId = parseInt(req.params.id);

  try {
    // Verify ownership
    const job = await prisma.job.findFirst({
      where: { id: jobId, employerId: req.user.id },
    });

    if (!job) {
      return res.status(404).json({ error: 'Job not found or access denied' });
    }

    await prisma.job.delete({
      where: { id: jobId },
    });

    res.json({ message: 'Job deleted successfully' });
  } catch (error) {
    console.error('Error deleting job:', error);
    res.status(500).json({ error: 'Failed to delete job' });
  }
});

// ============================================
// MATCHES ROUTES
// ============================================

// Candidate swipes on a job
app.post('/api/matches', authenticate, requireCandidate, async (req, res) => {
  const { jobId, status = 'LIKED' } = req.body;

  try {
    // Check if match already exists
    const existingMatch = await prisma.match.findUnique({
      where: {
        candidateId_jobId: {
          candidateId: req.user.id,
          jobId: parseInt(jobId),
        },
      },
    });

    if (existingMatch) {
      return res.status(400).json({ error: 'Already swiped on this job' });
    }

    const match = await prisma.match.create({
      data: {
        candidateId: req.user.id,
        jobId: parseInt(jobId),
        candidateStatus: status === 'LIKED' ? 'LIKED' : 'REJECTED',
      },
      include: {
        job: {
          include: {
            employer: {
              select: {
                companyName: true,
              },
            },
          },
        },
      },
    });

    res.status(201).json(match);
  } catch (error) {
    console.error('Error creating match:', error);
    res.status(500).json({ error: 'Failed to create match' });
  }
});

// Get user's matches (where both parties have approved)
app.get('/api/matches', authenticate, async (req, res) => {
  try {
    let matches;

    if (req.user.role === 'CANDIDATE') {
      matches = await prisma.match.findMany({
        where: {
          candidateId: req.user.id,
          isMatched: true,
        },
        include: {
          job: {
            include: {
              employer: {
                select: {
                  id: true,
                  companyName: true,
                  companyLogo: true,
                },
              },
            },
          },
          messages: {
            orderBy: { createdAt: 'desc' },
            take: 1,
          },
        },
        orderBy: { matchedAt: 'desc' },
      });
    } else {
      // Employer sees matches for their jobs
      matches = await prisma.match.findMany({
        where: {
          job: { employerId: req.user.id },
          isMatched: true,
        },
        include: {
          candidate: {
            select: {
              id: true,
              name: true,
              photo: true,
              field: true,
              bio: true,
            },
          },
          job: true,
          messages: {
            orderBy: { createdAt: 'desc' },
            take: 1,
          },
        },
        orderBy: { matchedAt: 'desc' },
      });
    }

    res.json(matches);
  } catch (error) {
    console.error('Error fetching matches:', error);
    res.status(500).json({ error: 'Failed to fetch matches' });
  }
});

// Get candidate's swipes (including pending)
app.get('/api/matches/swipes', authenticate, requireCandidate, async (req, res) => {
  try {
    const swipes = await prisma.match.findMany({
      where: {
        candidateId: req.user.id,
        candidateStatus: 'LIKED',
      },
      include: {
        job: {
          include: {
            employer: {
              select: {
                companyName: true,
                companyLogo: true,
              },
            },
          },
        },
      },
      orderBy: { createdAt: 'desc' },
    });

    res.json(swipes);
  } catch (error) {
    console.error('Error fetching swipes:', error);
    res.status(500).json({ error: 'Failed to fetch swipes' });
  }
});

// Get candidates who liked employer's jobs (pending review)
app.get('/api/employer/candidates', authenticate, requireEmployer, async (req, res) => {
  try {
    const candidates = await prisma.match.findMany({
      where: {
        job: { employerId: req.user.id },
        candidateStatus: 'LIKED',
        employerStatus: 'PENDING',
      },
      include: {
        candidate: {
          select: {
            id: true,
            name: true,
            email: true,
            photo: true,
            bio: true,
            field: true,
            salary: true,
            workModel: true,
          },
        },
        job: {
          select: {
            id: true,
            title: true,
          },
        },
      },
      orderBy: { createdAt: 'desc' },
    });

    res.json(candidates);
  } catch (error) {
    console.error('Error fetching candidates:', error);
    res.status(500).json({ error: 'Failed to fetch candidates' });
  }
});

// Employer approves a candidate
app.post('/api/matches/:id/approve', authenticate, requireEmployer, async (req, res) => {
  const matchId = parseInt(req.params.id);

  try {
    // Verify the match belongs to employer's job
    const match = await prisma.match.findFirst({
      where: {
        id: matchId,
        job: { employerId: req.user.id },
      },
    });

    if (!match) {
      return res.status(404).json({ error: 'Match not found' });
    }

    const updatedMatch = await prisma.match.update({
      where: { id: matchId },
      data: {
        employerStatus: 'APPROVED',
        isMatched: true,
        matchedAt: new Date(),
      },
      include: {
        candidate: {
          select: {
            id: true,
            name: true,
            photo: true,
          },
        },
        job: true,
      },
    });

    res.json(updatedMatch);
  } catch (error) {
    console.error('Error approving match:', error);
    res.status(500).json({ error: 'Failed to approve match' });
  }
});

// Employer rejects a candidate
app.post('/api/matches/:id/reject', authenticate, requireEmployer, async (req, res) => {
  const matchId = parseInt(req.params.id);

  try {
    // Verify the match belongs to employer's job
    const match = await prisma.match.findFirst({
      where: {
        id: matchId,
        job: { employerId: req.user.id },
      },
    });

    if (!match) {
      return res.status(404).json({ error: 'Match not found' });
    }

    const updatedMatch = await prisma.match.update({
      where: { id: matchId },
      data: {
        employerStatus: 'REJECTED',
      },
    });

    res.json(updatedMatch);
  } catch (error) {
    console.error('Error rejecting match:', error);
    res.status(500).json({ error: 'Failed to reject match' });
  }
});

// ============================================
// MESSAGES ROUTES
// ============================================

// Get messages for a match
app.get('/api/matches/:matchId/messages', authenticate, async (req, res) => {
  const matchId = parseInt(req.params.matchId);

  try {
    // Verify user has access to this match
    const match = await prisma.match.findFirst({
      where: {
        id: matchId,
        isMatched: true,
        OR: [
          { candidateId: req.user.id },
          { job: { employerId: req.user.id } },
        ],
      },
    });

    if (!match) {
      return res.status(404).json({ error: 'Match not found or access denied' });
    }

    const messages = await prisma.message.findMany({
      where: { matchId },
      include: {
        sender: {
          select: {
            id: true,
            name: true,
            photo: true,
            companyName: true,
            companyLogo: true,
            role: true,
          },
        },
      },
      orderBy: { createdAt: 'asc' },
    });

    res.json(messages);
  } catch (error) {
    console.error('Error fetching messages:', error);
    res.status(500).json({ error: 'Failed to fetch messages' });
  }
});

// Send a message
app.post('/api/matches/:matchId/messages', authenticate, async (req, res) => {
  const matchId = parseInt(req.params.matchId);
  const { content } = req.body;

  try {
    if (!content || content.trim() === '') {
      return res.status(400).json({ error: 'Message content is required' });
    }

    // Verify user has access to this match
    const match = await prisma.match.findFirst({
      where: {
        id: matchId,
        isMatched: true,
        OR: [
          { candidateId: req.user.id },
          { job: { employerId: req.user.id } },
        ],
      },
    });

    if (!match) {
      return res.status(404).json({ error: 'Match not found or access denied' });
    }

    const message = await prisma.message.create({
      data: {
        content: content.trim(),
        senderId: req.user.id,
        matchId,
      },
      include: {
        sender: {
          select: {
            id: true,
            name: true,
            photo: true,
            companyName: true,
            companyLogo: true,
            role: true,
          },
        },
      },
    });

    res.status(201).json(message);
  } catch (error) {
    console.error('Error sending message:', error);
    res.status(500).json({ error: 'Failed to send message' });
  }
});

// Mark message as read
app.put('/api/messages/:id/read', authenticate, async (req, res) => {
  const messageId = parseInt(req.params.id);

  try {
    const message = await prisma.message.update({
      where: { id: messageId },
      data: { isRead: true },
    });

    res.json(message);
  } catch (error) {
    console.error('Error marking message as read:', error);
    res.status(500).json({ error: 'Failed to mark message as read' });
  }
});

// ============================================
// LEGACY ROUTES (for backward compatibility)
// ============================================

// Create a new user (legacy - from old onboarding)
app.post('/api/users', async (req, res) => {
  const { name, bio, field, salary, workModel, photo } = req.body;
  try {
    // For MVP, generating a fake unique email based on timestamp
    const email = `user_${Date.now()}@example.com`;
    const password = await bcrypt.hash('temp-password', 10);

    const newUser = await prisma.user.create({
      data: {
        name,
        email,
        password,
        bio,
        field,
        salary: parseInt(salary) || null,
        workModel,
        photo,
        role: 'CANDIDATE',
      },
    });

    const token = generateToken(newUser.id);
    res.json({ ...newUser, token });
  } catch (error) {
    console.error('Error creating user:', error);
    res.status(500).json({ error: 'Failed to create user' });
  }
});

// Get user matches (legacy)
app.get('/api/users/:userId/matches', async (req, res) => {
  const { userId } = req.params;
  try {
    const matches = await prisma.match.findMany({
      where: {
        candidateId: parseInt(userId),
        candidateStatus: 'LIKED',
      },
      include: { job: true },
    });
    res.json(matches);
  } catch (error) {
    res.status(500).json({ error: 'Failed to fetch matches' });
  }
});

// ============================================
// CATCH-ALL ROUTE
// ============================================

// The "catchall" handler: for any request that doesn't
// match one above, send back React's index.html file.
app.use((req, res) => {
  res.sendFile(path.join(__dirname, '../dist/index.html'));
});

// Start server
app.listen(PORT, '0.0.0.0', () => {
  console.log(`Server running on http://0.0.0.0:${PORT}`);
});

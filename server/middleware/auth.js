const jwt = require('jsonwebtoken');
const { PrismaClient } = require('@prisma/client');

const prisma = new PrismaClient();
const JWT_SECRET = process.env.JWT_SECRET || 'matcha-super-secret-key-change-in-production';

// Middleware to verify JWT token
const authenticate = async (req, res, next) => {
  try {
    const authHeader = req.headers.authorization;

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return res.status(401).json({ error: 'No token provided' });
    }

    const token = authHeader.split(' ')[1];

    const decoded = jwt.verify(token, JWT_SECRET);

    // Fetch user from database
    const user = await prisma.user.findUnique({
      where: { id: decoded.userId },
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

    if (!user) {
      return res.status(401).json({ error: 'User not found' });
    }

    req.user = user;
    next();
  } catch (error) {
    if (error.name === 'JsonWebTokenError') {
      return res.status(401).json({ error: 'Invalid token' });
    }
    if (error.name === 'TokenExpiredError') {
      return res.status(401).json({ error: 'Token expired' });
    }
    console.error('Auth middleware error:', error);
    return res.status(500).json({ error: 'Authentication failed' });
  }
};

// Middleware to check if user is an employer
const requireEmployer = (req, res, next) => {
  if (req.user.role !== 'EMPLOYER') {
    return res.status(403).json({ error: 'Access denied. Employers only.' });
  }
  next();
};

// Middleware to check if user is a candidate
const requireCandidate = (req, res, next) => {
  if (req.user.role !== 'CANDIDATE') {
    return res.status(403).json({ error: 'Access denied. Candidates only.' });
  }
  next();
};

// Generate JWT token
const generateToken = (userId) => {
  return jwt.sign({ userId }, JWT_SECRET, { expiresIn: '7d' });
};

module.exports = {
  authenticate,
  requireEmployer,
  requireCandidate,
  generateToken,
  JWT_SECRET,
};

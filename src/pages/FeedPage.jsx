import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import styled from 'styled-components';
import { Heart, X, MessageCircle, User, Loader } from 'lucide-react';
import SwipeDeck from '../components/features/Feed/SwipeDeck';
import { useAuth } from '../contexts/AuthContext';
import { jobsAPI, matchesAPI } from '../services/api';
import { dummyJobs } from '../utils/dummyData';
import logo from '../assets/LOGO.jpeg';

const PageContainer = styled.div`
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: ${({ theme }) => theme.colors.background};
  overflow: hidden;
`;

const Header = styled.header`
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: ${({ theme }) => theme.spacing.md} ${({ theme }) => theme.spacing.lg};
  background: ${({ theme }) => theme.colors.surface};
  box-shadow: ${({ theme }) => theme.shadows.sm};
  z-index: 50;
`;

const HeaderLogo = styled.img`
  height: 36px;
  width: 36px;
  border-radius: ${({ theme }) => theme.borderRadius.md};
  object-fit: cover;
`;

const IconButton = styled.button`
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  border-radius: ${({ theme }) => theme.borderRadius.full};
  color: ${({ theme }) => theme.colors.secondary};
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};
  position: relative;

  &:hover {
    background-color: ${({ theme }) => theme.colors.surfaceHover};
  }

  &:active {
    transform: scale(0.95);
  }
`;

const NotificationDot = styled.span`
  position: absolute;
  top: 8px;
  right: 8px;
  width: 10px;
  height: 10px;
  background-color: ${({ theme }) => theme.colors.error};
  border-radius: 50%;
  border: 2px solid ${({ theme }) => theme.colors.surface};
`;

const FeedContent = styled.div`
  flex: 1;
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: ${({ theme }) => theme.spacing.md};
  padding-bottom: 100px;
  overflow: hidden;
`;

const Controls = styled.div`
  position: absolute;
  bottom: ${({ theme }) => theme.spacing.xl};
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  gap: ${({ theme }) => theme.spacing.xl};
  z-index: 50;
  pointer-events: none;
`;

const ControlButton = styled.button`
  pointer-events: auto;
  width: 64px;
  height: 64px;
  border-radius: 50%;
  border: none;
  background: ${({ theme }) => theme.colors.surface};
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.normal};

  &:hover {
    transform: scale(1.1);
  }

  &:active {
    transform: scale(0.95);
  }

  &.like {
    color: ${({ theme }) => theme.colors.like};
    box-shadow: 0 8px 24px ${({ theme }) => theme.colors.likeGlow};

    &:hover {
      background-color: ${({ theme }) => theme.colors.like};
      color: white;
    }
  }

  &.nope {
    color: ${({ theme }) => theme.colors.nope};
    box-shadow: 0 8px 24px ${({ theme }) => theme.colors.nopeGlow};

    &:hover {
      background-color: ${({ theme }) => theme.colors.nope};
      color: white;
    }
  }
`;

const LoadingContainer = styled.div`
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: ${({ theme }) => theme.spacing.md};
  color: ${({ theme }) => theme.colors.textMuted};

  svg {
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
`;

const EmptyState = styled.div`
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: ${({ theme }) => theme.spacing.xl};
  color: ${({ theme }) => theme.colors.textMuted};

  h2 {
    font-size: ${({ theme }) => theme.typography.fontSizes.xl};
    color: ${({ theme }) => theme.colors.secondary};
    margin-bottom: ${({ theme }) => theme.spacing.sm};
  }

  p {
    font-size: ${({ theme }) => theme.typography.fontSizes.md};
    max-width: 280px;
  }
`;

const FeedPage = () => {
  const [jobs, setJobs] = useState([]);
  const [loading, setLoading] = useState(true);
  const [currentIndex, setCurrentIndex] = useState(0);
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuth();

  useEffect(() => {
    const fetchJobs = async () => {
      try {
        const data = await jobsAPI.getAll();
        if (data && data.length > 0) {
          setJobs(data);
        } else {
          setJobs(dummyJobs);
        }
      } catch (err) {
        console.error('API Error:', err);
        setJobs(dummyJobs);
      } finally {
        setLoading(false);
      }
    };

    fetchJobs();
  }, []);

  const handleSwipe = async (direction, job) => {
    console.log(`Swiped ${direction} on ${job.title}`);

    if (direction === 'right' && isAuthenticated) {
      try {
        await matchesAPI.createSwipe(job.id, 'LIKED');
      } catch (err) {
        console.error('Error creating match:', err);
      }
    }

    setCurrentIndex((prev) => prev + 1);
  };

  const handleButtonSwipe = (direction) => {
    // This would trigger the swipe animation
    // For now, just advance to the next card
    if (currentIndex < jobs.length) {
      handleSwipe(direction, jobs[currentIndex]);
    }
  };

  if (loading) {
    return (
      <PageContainer>
        <Header>
          <IconButton onClick={() => navigate('/profile')}>
            <User size={24} />
          </IconButton>
          <HeaderLogo src={logo} alt="Matcha" />
          <IconButton onClick={() => navigate('/matches')}>
            <MessageCircle size={24} />
          </IconButton>
        </Header>
        <FeedContent>
          <LoadingContainer>
            <Loader size={40} />
            <p>טוען משרות...</p>
          </LoadingContainer>
        </FeedContent>
      </PageContainer>
    );
  }

  return (
    <PageContainer>
      <Header>
        <IconButton onClick={() => navigate('/profile')}>
          <User size={24} />
        </IconButton>
        <HeaderLogo src={logo} alt="Matcha" />
        <IconButton onClick={() => navigate('/matches')}>
          <MessageCircle size={24} />
          <NotificationDot />
        </IconButton>
      </Header>

      <FeedContent>
        {jobs.length > 0 ? (
          <SwipeDeck jobs={jobs} onSwipe={handleSwipe} />
        ) : (
          <EmptyState>
            <h2>אין משרות זמינות</h2>
            <p>נסו לחזור מאוחר יותר או להרחיב את ההעדפות שלכם</p>
          </EmptyState>
        )}
      </FeedContent>

      <Controls>
        <ControlButton className="nope" onClick={() => handleButtonSwipe('left')}>
          <X size={32} />
        </ControlButton>
        <ControlButton className="like" onClick={() => handleButtonSwipe('right')}>
          <Heart size={32} fill="currentColor" />
        </ControlButton>
      </Controls>
    </PageContainer>
  );
};

export default FeedPage;

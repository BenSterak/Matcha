import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import styled from 'styled-components';
import {
  ArrowRight,
  Plus,
  Briefcase,
  Eye,
  Heart,
  Edit,
  MoreVertical,
  Pause,
  Play,
  Trash2,
  Loader,
} from 'lucide-react';
import { jobsAPI } from '../../services/api';
import { Button, Badge } from '../../components/ui';

const PageContainer = styled.div`
  min-height: 100vh;
  background-color: ${({ theme }) => theme.colors.background};
`;

const Header = styled.header`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.md};
  padding: ${({ theme }) => theme.spacing.md} ${({ theme }) => theme.spacing.lg};
  background: white;
  border-bottom: 1px solid ${({ theme }) => theme.colors.borderLight};
`;

const BackButton = styled.button`
  width: 40px;
  height: 40px;
  border-radius: ${({ theme }) => theme.borderRadius.full};
  border: none;
  background: none;
  color: ${({ theme }) => theme.colors.text};
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color ${({ theme }) => theme.transitions.fast};

  &:hover {
    background-color: ${({ theme }) => theme.colors.surfaceHover};
  }
`;

const HeaderTitle = styled.h1`
  flex: 1;
  font-size: ${({ theme }) => theme.typography.fontSizes.xl};
  font-weight: ${({ theme }) => theme.typography.fontWeights.bold};
  color: ${({ theme }) => theme.colors.secondary};
`;

const Content = styled.main`
  padding: ${({ theme }) => theme.spacing.lg};
`;

const AddButton = styled(Link)`
  display: flex;
  align-items: center;
  justify-content: center;
  gap: ${({ theme }) => theme.spacing.sm};
  padding: ${({ theme }) => theme.spacing.md};
  margin-bottom: ${({ theme }) => theme.spacing.lg};
  background: white;
  border: 2px dashed ${({ theme }) => theme.colors.primary};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  color: ${({ theme }) => theme.colors.primary};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  text-decoration: none;
  transition: all ${({ theme }) => theme.transitions.fast};

  &:hover {
    background: ${({ theme }) => theme.colors.primaryLight};
  }
`;

const JobsList = styled.div`
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.md};
`;

const JobCard = styled.div`
  background: white;
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  padding: ${({ theme }) => theme.spacing.md};
  box-shadow: ${({ theme }) => theme.shadows.sm};
`;

const JobHeader = styled.div`
  display: flex;
  align-items: flex-start;
  gap: ${({ theme }) => theme.spacing.md};
`;

const JobIcon = styled.div`
  width: 48px;
  height: 48px;
  border-radius: ${({ theme }) => theme.borderRadius.md};
  background: ${({ theme }) => theme.colors.primaryLight};
  color: ${({ theme }) => theme.colors.primary};
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
`;

const JobInfo = styled.div`
  flex: 1;
  min-width: 0;
`;

const JobTitle = styled.h3`
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.text};
  margin-bottom: ${({ theme }) => theme.spacing.xs};
`;

const JobMeta = styled.div`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.md};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  color: ${({ theme }) => theme.colors.textMuted};
`;

const StatusBadge = styled.span`
  display: inline-flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  padding: ${({ theme }) => theme.spacing.xs} ${({ theme }) => theme.spacing.sm};
  border-radius: ${({ theme }) => theme.borderRadius.full};
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};

  ${({ $status, theme }) =>
    $status === 'active'
      ? `
        background: ${theme.colors.primaryLight};
        color: ${theme.colors.primaryDark};
      `
      : `
        background: ${theme.colors.background};
        color: ${theme.colors.textMuted};
      `}
`;

const MenuButton = styled.button`
  width: 32px;
  height: 32px;
  border-radius: ${({ theme }) => theme.borderRadius.md};
  border: none;
  background: none;
  color: ${({ theme }) => theme.colors.textMuted};
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};

  &:hover {
    background: ${({ theme }) => theme.colors.background};
    color: ${({ theme }) => theme.colors.text};
  }
`;

const JobStats = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.lg};
  padding-top: ${({ theme }) => theme.spacing.md};
  margin-top: ${({ theme }) => theme.spacing.md};
  border-top: 1px solid ${({ theme }) => theme.colors.borderLight};
`;

const StatItem = styled.div`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  color: ${({ theme }) => theme.colors.textMuted};

  svg {
    color: ${({ theme }) => theme.colors.primary};
  }
`;

const JobActions = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.sm};
  padding-top: ${({ theme }) => theme.spacing.md};
`;

const ActionButton = styled.button`
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: ${({ theme }) => theme.spacing.xs};
  padding: ${({ theme }) => theme.spacing.sm};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  border: 1px solid ${({ theme }) => theme.colors.borderLight};
  background: white;
  color: ${({ theme }) => theme.colors.text};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};

  &:hover {
    background: ${({ theme }) => theme.colors.surfaceHover};
  }

  ${({ $variant, theme }) =>
    $variant === 'danger' &&
    `
    color: ${theme.colors.error};
    border-color: ${theme.colors.error};

    &:hover {
      background: ${theme.colors.error};
      color: white;
    }
  `}
`;

const EmptyState = styled.div`
  text-align: center;
  padding: ${({ theme }) => theme.spacing.xxl};
  color: ${({ theme }) => theme.colors.textMuted};

  svg {
    margin-bottom: ${({ theme }) => theme.spacing.md};
    color: ${({ theme }) => theme.colors.textLight};
  }

  h2 {
    font-size: ${({ theme }) => theme.typography.fontSizes.lg};
    color: ${({ theme }) => theme.colors.secondary};
    margin-bottom: ${({ theme }) => theme.spacing.sm};
  }

  p {
    margin-bottom: ${({ theme }) => theme.spacing.lg};
  }
`;

const LoadingContainer = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
  padding: ${({ theme }) => theme.spacing.xxl};
  color: ${({ theme }) => theme.colors.textMuted};

  svg {
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }
`;

// Mock data
const mockJobs = [
  {
    id: 1,
    title: 'Frontend Developer',
    location: 'תל אביב',
    type: 'full-time',
    views: 156,
    likes: 23,
    status: 'active',
    createdAt: '2024-01-15',
  },
  {
    id: 2,
    title: 'Product Manager',
    location: 'רמת גן',
    type: 'full-time',
    views: 89,
    likes: 12,
    status: 'active',
    createdAt: '2024-01-10',
  },
  {
    id: 3,
    title: 'UX Designer',
    location: 'הרצליה',
    type: 'part-time',
    views: 67,
    likes: 8,
    status: 'paused',
    createdAt: '2024-01-05',
  },
];

const typeLabels = {
  'full-time': 'משרה מלאה',
  'part-time': 'חלקית',
  'contract': 'פרילנס',
};

const MyJobsPage = () => {
  const navigate = useNavigate();
  const [jobs, setJobs] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchJobs = async () => {
      try {
        const data = await jobsAPI.getMyJobs();
        if (data && data.length > 0) {
          setJobs(data);
        } else {
          setJobs(mockJobs);
        }
      } catch (error) {
        console.error('Error fetching jobs:', error);
        setJobs(mockJobs);
      } finally {
        setLoading(false);
      }
    };

    fetchJobs();
  }, []);

  const handleToggleStatus = async (jobId, currentStatus) => {
    const newStatus = currentStatus === 'active' ? 'paused' : 'active';
    try {
      await jobsAPI.update(jobId, { status: newStatus });
      setJobs((prev) =>
        prev.map((job) =>
          job.id === jobId ? { ...job, status: newStatus } : job
        )
      );
    } catch (error) {
      console.error('Error updating job status:', error);
      // Update locally anyway for demo
      setJobs((prev) =>
        prev.map((job) =>
          job.id === jobId ? { ...job, status: newStatus } : job
        )
      );
    }
  };

  const handleDelete = async (jobId) => {
    if (window.confirm('האם אתה בטוח שברצונך למחוק את המשרה?')) {
      try {
        await jobsAPI.delete(jobId);
        setJobs((prev) => prev.filter((job) => job.id !== jobId));
      } catch (error) {
        console.error('Error deleting job:', error);
        // Delete locally anyway for demo
        setJobs((prev) => prev.filter((job) => job.id !== jobId));
      }
    }
  };

  return (
    <PageContainer>
      <Header>
        <BackButton onClick={() => navigate('/employer/dashboard')}>
          <ArrowRight size={24} />
        </BackButton>
        <HeaderTitle>המשרות שלי</HeaderTitle>
      </Header>

      <Content>
        <AddButton to="/employer/jobs/new">
          <Plus size={20} />
          פרסום משרה חדשה
        </AddButton>

        {loading ? (
          <LoadingContainer>
            <Loader size={40} />
          </LoadingContainer>
        ) : jobs.length === 0 ? (
          <EmptyState>
            <Briefcase size={64} />
            <h2>עדיין לא פרסמת משרות</h2>
            <p>התחל לגייס עובדים חדשים על ידי פרסום המשרה הראשונה שלך</p>
            <Button as={Link} to="/employer/jobs/new" icon={<Plus size={18} />}>
              פרסום משרה
            </Button>
          </EmptyState>
        ) : (
          <JobsList>
            {jobs.map((job) => (
              <JobCard key={job.id}>
                <JobHeader>
                  <JobIcon>
                    <Briefcase size={24} />
                  </JobIcon>
                  <JobInfo>
                    <JobTitle>{job.title}</JobTitle>
                    <JobMeta>
                      <span>{job.location}</span>
                      <span>•</span>
                      <span>{typeLabels[job.type] || job.type}</span>
                      <StatusBadge $status={job.status}>
                        {job.status === 'active' ? 'פעילה' : 'מושהית'}
                      </StatusBadge>
                    </JobMeta>
                  </JobInfo>
                </JobHeader>

                <JobStats>
                  <StatItem>
                    <Eye size={16} />
                    {job.views || 0} צפיות
                  </StatItem>
                  <StatItem>
                    <Heart size={16} />
                    {job.likes || 0} לייקים
                  </StatItem>
                </JobStats>

                <JobActions>
                  <ActionButton onClick={() => navigate(`/employer/jobs/${job.id}`)}>
                    <Edit size={16} />
                    עריכה
                  </ActionButton>
                  <ActionButton onClick={() => handleToggleStatus(job.id, job.status)}>
                    {job.status === 'active' ? (
                      <>
                        <Pause size={16} />
                        השהיה
                      </>
                    ) : (
                      <>
                        <Play size={16} />
                        הפעלה
                      </>
                    )}
                  </ActionButton>
                  <ActionButton $variant="danger" onClick={() => handleDelete(job.id)}>
                    <Trash2 size={16} />
                  </ActionButton>
                </JobActions>
              </JobCard>
            ))}
          </JobsList>
        )}
      </Content>
    </PageContainer>
  );
};

export default MyJobsPage;

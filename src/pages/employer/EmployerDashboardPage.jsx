import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import styled from 'styled-components';
import {
  Briefcase,
  Users,
  MessageCircle,
  Plus,
  TrendingUp,
  Eye,
  Heart,
  Settings,
  LogOut,
} from 'lucide-react';
import { useAuth } from '../../contexts/AuthContext';
import { jobsAPI, matchesAPI } from '../../services/api';
import { Avatar, Card, Button, Badge } from '../../components/ui';

const PageContainer = styled.div`
  min-height: 100vh;
  background-color: ${({ theme }) => theme.colors.background};
`;

const Header = styled.header`
  background: white;
  padding: ${({ theme }) => theme.spacing.lg};
  border-bottom: 1px solid ${({ theme }) => theme.colors.borderLight};
`;

const HeaderTop = styled.div`
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: ${({ theme }) => theme.spacing.md};
`;

const WelcomeText = styled.div`
  h1 {
    font-size: ${({ theme }) => theme.typography.fontSizes.xl};
    font-weight: ${({ theme }) => theme.typography.fontWeights.bold};
    color: ${({ theme }) => theme.colors.secondary};
    margin-bottom: ${({ theme }) => theme.spacing.xs};
  }

  p {
    font-size: ${({ theme }) => theme.typography.fontSizes.sm};
    color: ${({ theme }) => theme.colors.textMuted};
  }
`;

const HeaderActions = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.sm};
`;

const IconButton = styled.button`
  width: 40px;
  height: 40px;
  border-radius: ${({ theme }) => theme.borderRadius.full};
  border: none;
  background: ${({ theme }) => theme.colors.background};
  color: ${({ theme }) => theme.colors.textMuted};
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};

  &:hover {
    background: ${({ theme }) => theme.colors.surfaceHover};
    color: ${({ theme }) => theme.colors.text};
  }
`;

const StatsGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: ${({ theme }) => theme.spacing.md};
  margin-top: ${({ theme }) => theme.spacing.md};
`;

const StatCard = styled.div`
  background: ${({ theme }) => theme.colors.surface};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  padding: ${({ theme }) => theme.spacing.md};
  text-align: center;

  .stat-value {
    font-size: ${({ theme }) => theme.typography.fontSizes['2xl']};
    font-weight: ${({ theme }) => theme.typography.fontWeights.bold};
    color: ${({ theme }) => theme.colors.primary};
    margin-bottom: ${({ theme }) => theme.spacing.xs};
  }

  .stat-label {
    font-size: ${({ theme }) => theme.typography.fontSizes.xs};
    color: ${({ theme }) => theme.colors.textMuted};
  }
`;

const Content = styled.main`
  padding: ${({ theme }) => theme.spacing.lg};
`;

const Section = styled.section`
  margin-bottom: ${({ theme }) => theme.spacing.xl};
`;

const SectionHeader = styled.div`
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: ${({ theme }) => theme.spacing.md};

  h2 {
    font-size: ${({ theme }) => theme.typography.fontSizes.lg};
    font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
    color: ${({ theme }) => theme.colors.secondary};
  }
`;

const SeeAllLink = styled(Link)`
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  color: ${({ theme }) => theme.colors.primary};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  text-decoration: none;

  &:hover {
    text-decoration: underline;
  }
`;

const JobsList = styled.div`
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.md};
`;

const JobItem = styled(Link)`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.md};
  padding: ${({ theme }) => theme.spacing.md};
  background: white;
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  text-decoration: none;
  transition: box-shadow ${({ theme }) => theme.transitions.fast};

  &:hover {
    box-shadow: ${({ theme }) => theme.shadows.md};
  }
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
`;

const JobInfo = styled.div`
  flex: 1;

  h3 {
    font-size: ${({ theme }) => theme.typography.fontSizes.md};
    font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
    color: ${({ theme }) => theme.colors.text};
    margin-bottom: ${({ theme }) => theme.spacing.xs};
  }

  p {
    font-size: ${({ theme }) => theme.typography.fontSizes.sm};
    color: ${({ theme }) => theme.colors.textMuted};
  }
`;

const JobStats = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.md};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  color: ${({ theme }) => theme.colors.textMuted};

  span {
    display: flex;
    align-items: center;
    gap: ${({ theme }) => theme.spacing.xs};
  }
`;

const CandidatesList = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.md};
  overflow-x: auto;
  padding-bottom: ${({ theme }) => theme.spacing.sm};

  &::-webkit-scrollbar {
    display: none;
  }
`;

const CandidateCard = styled.div`
  min-width: 160px;
  background: white;
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  padding: ${({ theme }) => theme.spacing.md};
  text-align: center;
  cursor: pointer;
  transition: box-shadow ${({ theme }) => theme.transitions.fast};

  &:hover {
    box-shadow: ${({ theme }) => theme.shadows.md};
  }
`;

const CandidateName = styled.h4`
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.text};
  margin-top: ${({ theme }) => theme.spacing.sm};
`;

const CandidateRole = styled.p`
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme }) => theme.colors.textMuted};
  margin-top: ${({ theme }) => theme.spacing.xs};
`;

const EmptyState = styled.div`
  text-align: center;
  padding: ${({ theme }) => theme.spacing.xl};
  color: ${({ theme }) => theme.colors.textMuted};

  p {
    margin-bottom: ${({ theme }) => theme.spacing.md};
  }
`;

const QuickActions = styled.div`
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: ${({ theme }) => theme.spacing.md};
`;

const QuickAction = styled(Link)`
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.sm};
  padding: ${({ theme }) => theme.spacing.lg};
  background: white;
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  text-decoration: none;
  transition: box-shadow ${({ theme }) => theme.transitions.fast};

  &:hover {
    box-shadow: ${({ theme }) => theme.shadows.md};
  }

  .icon {
    width: 48px;
    height: 48px;
    border-radius: ${({ theme }) => theme.borderRadius.full};
    background: ${({ $color, theme }) => $color || theme.colors.primaryLight};
    color: ${({ $iconColor, theme }) => $iconColor || theme.colors.primary};
    display: flex;
    align-items: center;
    justify-content: center;
  }

  span {
    font-size: ${({ theme }) => theme.typography.fontSizes.sm};
    font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
    color: ${({ theme }) => theme.colors.text};
  }
`;

// Mock data
const mockJobs = [
  { id: 1, title: 'Frontend Developer', views: 156, likes: 23, status: 'active' },
  { id: 2, title: 'Product Manager', views: 89, likes: 12, status: 'active' },
  { id: 3, title: 'UX Designer', views: 67, likes: 8, status: 'paused' },
];

const mockCandidates = [
  { id: 1, name: 'יוסי כהן', role: 'Frontend', photo: null },
  { id: 2, name: 'שרה לוי', role: 'Product', photo: null },
  { id: 3, name: 'דני אברהם', role: 'Design', photo: null },
];

const EmployerDashboardPage = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [jobs, setJobs] = useState([]);
  const [candidates, setCandidates] = useState([]);
  const [stats, setStats] = useState({ totalJobs: 0, totalCandidates: 0, totalMatches: 0 });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        // Fetch jobs
        const jobsData = await jobsAPI.getMyJobs();
        if (jobsData && jobsData.length > 0) {
          setJobs(jobsData);
        } else {
          setJobs(mockJobs);
        }

        // Fetch matches/candidates
        const matchesData = await matchesAPI.getMatches();
        if (matchesData && matchesData.length > 0) {
          const candidatesList = matchesData.map(m => ({
            id: m.id,
            name: m.candidate?.name || 'Unknown',
            role: m.job?.title || 'N/A',
            photo: m.candidate?.photo,
          }));
          setCandidates(candidatesList);
          setStats({
            totalJobs: jobsData?.length || 0,
            totalCandidates: matchesData.length,
            totalMatches: matchesData.filter(m => m.isMatched).length,
          });
        } else {
          setCandidates(mockCandidates);
          setStats({ totalJobs: 3, totalCandidates: 45, totalMatches: 12 });
        }
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
        setJobs(mockJobs);
        setCandidates(mockCandidates);
        setStats({ totalJobs: 3, totalCandidates: 45, totalMatches: 12 });
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  return (
    <PageContainer>
      <Header>
        <HeaderTop>
          <WelcomeText>
            <h1>שלום, {user?.companyName || user?.name || 'מעסיק'}</h1>
            <p>מה קורה בגיוסים שלך?</p>
          </WelcomeText>
          <HeaderActions>
            <IconButton onClick={() => navigate('/profile')}>
              <Settings size={20} />
            </IconButton>
            <IconButton onClick={handleLogout}>
              <LogOut size={20} />
            </IconButton>
          </HeaderActions>
        </HeaderTop>

        <StatsGrid>
          <StatCard>
            <div className="stat-value">{stats.totalJobs}</div>
            <div className="stat-label">משרות פעילות</div>
          </StatCard>
          <StatCard>
            <div className="stat-value">{stats.totalCandidates}</div>
            <div className="stat-label">מועמדים</div>
          </StatCard>
          <StatCard>
            <div className="stat-value">{stats.totalMatches}</div>
            <div className="stat-label">התאמות</div>
          </StatCard>
        </StatsGrid>
      </Header>

      <Content>
        <Section>
          <SectionHeader>
            <h2>פעולות מהירות</h2>
          </SectionHeader>
          <QuickActions>
            <QuickAction to="/employer/jobs/new">
              <div className="icon">
                <Plus size={24} />
              </div>
              <span>פרסום משרה</span>
            </QuickAction>
            <QuickAction to="/employer/candidates">
              <div className="icon" style={{ background: '#FEF3C7', color: '#D97706' }}>
                <Users size={24} />
              </div>
              <span>סקירת מועמדים</span>
            </QuickAction>
            <QuickAction to="/matches">
              <div className="icon" style={{ background: '#DBEAFE', color: '#2563EB' }}>
                <MessageCircle size={24} />
              </div>
              <span>הודעות</span>
            </QuickAction>
            <QuickAction to="/employer/jobs">
              <div className="icon" style={{ background: '#F3E8FF', color: '#9333EA' }}>
                <Briefcase size={24} />
              </div>
              <span>המשרות שלי</span>
            </QuickAction>
          </QuickActions>
        </Section>

        <Section>
          <SectionHeader>
            <h2>מועמדים ממתינים</h2>
            <SeeAllLink to="/employer/candidates">צפה בכל</SeeAllLink>
          </SectionHeader>
          {candidates.length > 0 ? (
            <CandidatesList>
              {candidates.slice(0, 5).map((candidate) => (
                <CandidateCard key={candidate.id} onClick={() => navigate('/employer/candidates')}>
                  <Avatar
                    src={candidate.photo}
                    name={candidate.name}
                    size="lg"
                  />
                  <CandidateName>{candidate.name}</CandidateName>
                  <CandidateRole>{candidate.role}</CandidateRole>
                </CandidateCard>
              ))}
            </CandidatesList>
          ) : (
            <EmptyState>
              <p>אין מועמדים ממתינים כרגע</p>
            </EmptyState>
          )}
        </Section>

        <Section>
          <SectionHeader>
            <h2>המשרות שלי</h2>
            <SeeAllLink to="/employer/jobs">צפה בכל</SeeAllLink>
          </SectionHeader>
          {jobs.length > 0 ? (
            <JobsList>
              {jobs.slice(0, 3).map((job) => (
                <JobItem key={job.id} to={`/employer/jobs/${job.id}`}>
                  <JobIcon>
                    <Briefcase size={24} />
                  </JobIcon>
                  <JobInfo>
                    <h3>{job.title}</h3>
                    <p>{job.status === 'active' ? 'פעילה' : 'מושהית'}</p>
                  </JobInfo>
                  <JobStats>
                    <span><Eye size={14} /> {job.views || 0}</span>
                    <span><Heart size={14} /> {job.likes || 0}</span>
                  </JobStats>
                </JobItem>
              ))}
            </JobsList>
          ) : (
            <EmptyState>
              <p>עדיין לא פרסמת משרות</p>
              <Button as={Link} to="/employer/jobs/new" icon={<Plus size={18} />}>
                פרסום משרה ראשונה
              </Button>
            </EmptyState>
          )}
        </Section>
      </Content>
    </PageContainer>
  );
};

export default EmployerDashboardPage;

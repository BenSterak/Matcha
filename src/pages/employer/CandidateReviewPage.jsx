import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import styled from 'styled-components';
import { motion, AnimatePresence } from 'framer-motion';
import {
  ArrowRight,
  Check,
  X,
  Briefcase,
  MapPin,
  DollarSign,
  User,
  FileText,
  Loader,
} from 'lucide-react';
import { useAuth } from '../../contexts/AuthContext';
import { matchesAPI } from '../../services/api';
import { Avatar, Button, Badge } from '../../components/ui';

const PageContainer = styled.div`
  min-height: 100vh;
  background-color: ${({ theme }) => theme.colors.background};
  display: flex;
  flex-direction: column;
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

const CountBadge = styled.span`
  background: ${({ theme }) => theme.colors.primary};
  color: white;
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  padding: ${({ theme }) => theme.spacing.xs} ${({ theme }) => theme.spacing.sm};
  border-radius: ${({ theme }) => theme.borderRadius.full};
`;

const Content = styled.main`
  flex: 1;
  display: flex;
  flex-direction: column;
  padding: ${({ theme }) => theme.spacing.lg};
`;

const CardContainer = styled.div`
  flex: 1;
  position: relative;
  max-width: 400px;
  margin: 0 auto;
  width: 100%;
`;

const CandidateCard = styled(motion.div)`
  position: absolute;
  width: 100%;
  background: white;
  border-radius: ${({ theme }) => theme.borderRadius.xl};
  box-shadow: ${({ theme }) => theme.shadows.xl};
  overflow: hidden;
`;

const CardHeader = styled.div`
  padding: ${({ theme }) => theme.spacing.xl};
  text-align: center;
  background: linear-gradient(
    180deg,
    ${({ theme }) => theme.colors.primaryLight} 0%,
    white 100%
  );
`;

const CandidateName = styled.h2`
  font-size: ${({ theme }) => theme.typography.fontSizes.xl};
  font-weight: ${({ theme }) => theme.typography.fontWeights.bold};
  color: ${({ theme }) => theme.colors.secondary};
  margin-top: ${({ theme }) => theme.spacing.md};
`;

const CandidateRole = styled.p`
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  color: ${({ theme }) => theme.colors.textMuted};
  margin-top: ${({ theme }) => theme.spacing.xs};
`;

const CardContent = styled.div`
  padding: ${({ theme }) => theme.spacing.lg};
`;

const InfoRow = styled.div`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.sm};
  padding: ${({ theme }) => theme.spacing.sm} 0;
  color: ${({ theme }) => theme.colors.text};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};

  svg {
    color: ${({ theme }) => theme.colors.primary};
  }
`;

const BioSection = styled.div`
  margin-top: ${({ theme }) => theme.spacing.md};
  padding-top: ${({ theme }) => theme.spacing.md};
  border-top: 1px solid ${({ theme }) => theme.colors.borderLight};

  h3 {
    font-size: ${({ theme }) => theme.typography.fontSizes.sm};
    font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
    color: ${({ theme }) => theme.colors.secondary};
    margin-bottom: ${({ theme }) => theme.spacing.xs};
  }

  p {
    font-size: ${({ theme }) => theme.typography.fontSizes.sm};
    color: ${({ theme }) => theme.colors.textMuted};
    line-height: ${({ theme }) => theme.typography.lineHeights.relaxed};
  }
`;

const AppliedFor = styled.div`
  margin-top: ${({ theme }) => theme.spacing.md};
  padding: ${({ theme }) => theme.spacing.sm} ${({ theme }) => theme.spacing.md};
  background: ${({ theme }) => theme.colors.primaryLight};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  color: ${({ theme }) => theme.colors.primaryDark};
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
`;

const ActionsContainer = styled.div`
  display: flex;
  justify-content: center;
  gap: ${({ theme }) => theme.spacing.xl};
  padding: ${({ theme }) => theme.spacing.xl};
`;

const ActionButton = styled.button`
  width: 64px;
  height: 64px;
  border-radius: ${({ theme }) => theme.borderRadius.full};
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};
  box-shadow: ${({ theme }) => theme.shadows.lg};

  ${({ $variant, theme }) =>
    $variant === 'reject'
      ? `
        background: white;
        color: ${theme.colors.error};
        border: 2px solid ${theme.colors.error};

        &:hover {
          background: ${theme.colors.error};
          color: white;
        }
      `
      : `
        background: ${theme.colors.primary};
        color: white;

        &:hover {
          background: ${theme.colors.primaryDark};
          transform: scale(1.05);
        }
      `}
`;

const EmptyState = styled.div`
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
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
`;

const LoadingContainer = styled.div`
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
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
const mockCandidates = [
  {
    id: 1,
    name: 'יוסי כהן',
    photo: null,
    field: 'פיתוח תוכנה',
    salary: 25000,
    workModel: 'hybrid',
    bio: 'מפתח Full Stack עם 5 שנות ניסיון. מתמחה ב-React ו-Node.js. אוהב אתגרים טכנולוגיים ועבודת צוות.',
    jobTitle: 'Frontend Developer',
  },
  {
    id: 2,
    name: 'שרה לוי',
    photo: null,
    field: 'עיצוב גרפי / UI/UX',
    salary: 18000,
    workModel: 'remote',
    bio: 'מעצבת UX/UI עם תשוקה ליצירת חוויות משתמש מעולות. ניסיון בעבודה עם סטארטאפים וחברות גדולות.',
    jobTitle: 'UX Designer',
  },
  {
    id: 3,
    name: 'דני אברהם',
    photo: null,
    field: 'שיווק ודיגיטל',
    salary: 15000,
    workModel: 'office',
    bio: 'מנהל שיווק דיגיטלי עם התמחות בקמפיינים ממומנים וSEO. בעל יכולת אנליטית גבוהה.',
    jobTitle: 'Product Manager',
  },
];

const fieldLabels = {
  dev: 'פיתוח תוכנה',
  design: 'עיצוב גרפי / UI/UX',
  marketing: 'שיווק ודיגיטל',
  sales: 'מכירות',
  admin: 'אדמיניסטרציה',
};

const workModelLabels = {
  office: 'משרד',
  hybrid: 'היברידי',
  remote: 'מהבית',
};

const CandidateReviewPage = () => {
  const navigate = useNavigate();
  const [candidates, setCandidates] = useState([]);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [loading, setLoading] = useState(true);
  const [direction, setDirection] = useState(0);

  useEffect(() => {
    const fetchCandidates = async () => {
      try {
        const matches = await matchesAPI.getMatches();
        if (matches && matches.length > 0) {
          // Filter for pending candidates (liked but not yet approved/rejected)
          const pending = matches
            .filter((m) => m.candidateStatus === 'LIKED' && !m.isMatched)
            .map((m) => ({
              id: m.id,
              name: m.candidate?.name || 'Unknown',
              photo: m.candidate?.photo,
              field: fieldLabels[m.candidate?.field] || m.candidate?.field,
              salary: m.candidate?.salary,
              workModel: m.candidate?.workModel,
              bio: m.candidate?.bio,
              jobTitle: m.job?.title,
            }));
          setCandidates(pending.length > 0 ? pending : mockCandidates);
        } else {
          setCandidates(mockCandidates);
        }
      } catch (error) {
        console.error('Error fetching candidates:', error);
        setCandidates(mockCandidates);
      } finally {
        setLoading(false);
      }
    };

    fetchCandidates();
  }, []);

  const handleAction = async (action) => {
    const candidate = candidates[currentIndex];
    setDirection(action === 'approve' ? 1 : -1);

    try {
      if (action === 'approve') {
        await matchesAPI.approve(candidate.id);
      } else {
        await matchesAPI.reject(candidate.id);
      }
    } catch (error) {
      console.error(`Error ${action}ing candidate:`, error);
    }

    setTimeout(() => {
      setCurrentIndex((prev) => prev + 1);
      setDirection(0);
    }, 300);
  };

  if (loading) {
    return (
      <PageContainer>
        <Header>
          <BackButton onClick={() => navigate('/employer/dashboard')}>
            <ArrowRight size={24} />
          </BackButton>
          <HeaderTitle>סקירת מועמדים</HeaderTitle>
        </Header>
        <LoadingContainer>
          <Loader size={40} />
        </LoadingContainer>
      </PageContainer>
    );
  }

  const currentCandidate = candidates[currentIndex];
  const remainingCount = candidates.length - currentIndex;

  return (
    <PageContainer>
      <Header>
        <BackButton onClick={() => navigate('/employer/dashboard')}>
          <ArrowRight size={24} />
        </BackButton>
        <HeaderTitle>סקירת מועמדים</HeaderTitle>
        {remainingCount > 0 && <CountBadge>{remainingCount}</CountBadge>}
      </Header>

      <Content>
        {currentIndex >= candidates.length ? (
          <EmptyState>
            <User size={64} />
            <h2>סיימת לסקור את כל המועמדים</h2>
            <p>חזור מאוחר יותר לראות מועמדים חדשים</p>
            <Button
              onClick={() => navigate('/employer/dashboard')}
              style={{ marginTop: '1rem' }}
            >
              חזרה לדשבורד
            </Button>
          </EmptyState>
        ) : (
          <>
            <CardContainer>
              <AnimatePresence>
                <CandidateCard
                  key={currentCandidate.id}
                  initial={{ scale: 0.95, opacity: 0 }}
                  animate={{ scale: 1, opacity: 1, x: 0 }}
                  exit={{
                    x: direction > 0 ? 300 : -300,
                    opacity: 0,
                    transition: { duration: 0.3 },
                  }}
                >
                  <CardHeader>
                    <Avatar
                      src={currentCandidate.photo}
                      name={currentCandidate.name}
                      size="xl"
                    />
                    <CandidateName>{currentCandidate.name}</CandidateName>
                    <CandidateRole>{currentCandidate.field}</CandidateRole>
                  </CardHeader>

                  <CardContent>
                    <InfoRow>
                      <DollarSign size={16} />
                      ₪{parseInt(currentCandidate.salary || 0).toLocaleString()} / חודש
                    </InfoRow>
                    <InfoRow>
                      <MapPin size={16} />
                      {workModelLabels[currentCandidate.workModel] || currentCandidate.workModel}
                    </InfoRow>

                    {currentCandidate.bio && (
                      <BioSection>
                        <h3>קצת עליי</h3>
                        <p>{currentCandidate.bio}</p>
                      </BioSection>
                    )}

                    {currentCandidate.jobTitle && (
                      <AppliedFor>
                        <Briefcase size={14} />
                        מתעניין/ת ב: {currentCandidate.jobTitle}
                      </AppliedFor>
                    )}
                  </CardContent>
                </CandidateCard>
              </AnimatePresence>
            </CardContainer>

            <ActionsContainer>
              <ActionButton $variant="reject" onClick={() => handleAction('reject')}>
                <X size={32} />
              </ActionButton>
              <ActionButton onClick={() => handleAction('approve')}>
                <Check size={32} />
              </ActionButton>
            </ActionsContainer>
          </>
        )}
      </Content>
    </PageContainer>
  );
};

export default CandidateReviewPage;

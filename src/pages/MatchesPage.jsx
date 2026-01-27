import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import styled from 'styled-components';
import { ArrowRight, MessageCircle, Search, Loader } from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';
import { matchesAPI } from '../services/api';
import { Avatar, Badge } from '../components/ui';
import ChatInterface from '../components/features/Matches/ChatInterface';

const PageContainer = styled.div`
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: ${({ theme }) => theme.colors.surface};
`;

const Header = styled.header`
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: ${({ theme }) => theme.spacing.md} ${({ theme }) => theme.spacing.lg};
  border-bottom: 1px solid ${({ theme }) => theme.colors.borderLight};
`;

const BackButton = styled.button`
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: none;
  border: none;
  border-radius: ${({ theme }) => theme.borderRadius.full};
  color: ${({ theme }) => theme.colors.text};
  cursor: pointer;
  transition: background-color ${({ theme }) => theme.transitions.fast};

  &:hover {
    background-color: ${({ theme }) => theme.colors.surfaceHover};
  }
`;

const HeaderTitle = styled.h1`
  font-size: ${({ theme }) => theme.typography.fontSizes.xl};
  font-weight: ${({ theme }) => theme.typography.fontWeights.bold};
  color: ${({ theme }) => theme.colors.secondary};
`;

const Spacer = styled.div`
  width: 40px;
`;

const SearchContainer = styled.div`
  padding: ${({ theme }) => theme.spacing.md} ${({ theme }) => theme.spacing.lg};
`;

const SearchInput = styled.div`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.sm};
  padding: ${({ theme }) => theme.spacing.sm} ${({ theme }) => theme.spacing.md};
  background-color: ${({ theme }) => theme.colors.background};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  color: ${({ theme }) => theme.colors.textMuted};

  input {
    flex: 1;
    border: none;
    background: none;
    font-size: ${({ theme }) => theme.typography.fontSizes.md};
    color: ${({ theme }) => theme.colors.text};

    &::placeholder {
      color: ${({ theme }) => theme.colors.textLight};
    }
  }
`;

const MatchesScrollContainer = styled.div`
  padding: ${({ theme }) => theme.spacing.md} ${({ theme }) => theme.spacing.lg};
  border-bottom: 1px solid ${({ theme }) => theme.colors.borderLight};
`;

const SectionTitle = styled.h2`
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.textMuted};
  margin-bottom: ${({ theme }) => theme.spacing.md};
  text-transform: uppercase;
  letter-spacing: 0.05em;
`;

const MatchesRow = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.md};
  overflow-x: auto;
  padding-bottom: ${({ theme }) => theme.spacing.sm};

  &::-webkit-scrollbar {
    display: none;
  }
`;

const MatchAvatar = styled.button`
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  background: none;
  border: none;
  cursor: pointer;
  min-width: 72px;
`;

const MatchName = styled.span`
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme }) => theme.colors.text};
  max-width: 72px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
`;

const ConversationsList = styled.div`
  flex: 1;
  overflow-y: auto;
`;

const ConversationItem = styled.button`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.md};
  width: 100%;
  padding: ${({ theme }) => theme.spacing.md} ${({ theme }) => theme.spacing.lg};
  background: none;
  border: none;
  text-align: right;
  cursor: pointer;
  transition: background-color ${({ theme }) => theme.transitions.fast};

  &:hover {
    background-color: ${({ theme }) => theme.colors.surfaceHover};
  }
`;

const ConversationInfo = styled.div`
  flex: 1;
  min-width: 0;
`;

const ConversationName = styled.h3`
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.text};
  margin-bottom: 2px;
`;

const ConversationPreview = styled.p`
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  color: ${({ theme }) => theme.colors.textMuted};
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
`;

const ConversationMeta = styled.div`
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: ${({ theme }) => theme.spacing.xs};
`;

const TimeStamp = styled.span`
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme }) => theme.colors.textLight};
`;

const LoadingContainer = styled.div`
  flex: 1;
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
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: ${({ theme }) => theme.spacing.xl};
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
    font-size: ${({ theme }) => theme.typography.fontSizes.md};
    max-width: 280px;
  }
`;

// Mock data for demo
const mockMatches = [
  {
    id: 1,
    company: 'TechStart Ltd.',
    companyLogo: 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=400&q=80',
    lastMessage: 'היי, ראינו שאהבת את המשרה שלנו! נשמח לקבוע ראיון.',
    lastActive: '10:30',
    hasUnread: true,
    isOnline: true,
  },
  {
    id: 2,
    company: 'Creative Studio',
    companyLogo: 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=400&q=80',
    lastMessage: 'שלחתי לך הזמנה לראיון במייל.',
    lastActive: 'אתמול',
    hasUnread: false,
    isOnline: false,
  },
  {
    id: 3,
    company: 'DataFlow Inc.',
    companyLogo: 'https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=400&q=80',
    lastMessage: 'תודה על ההתעניינות! נחזור אליך בקרוב.',
    lastActive: 'לפני שבוע',
    hasUnread: false,
    isOnline: false,
  },
];

const MatchesPage = () => {
  const [matches, setMatches] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedMatch, setSelectedMatch] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const navigate = useNavigate();
  const { isAuthenticated, isEmployer } = useAuth();

  useEffect(() => {
    const fetchMatches = async () => {
      if (!isAuthenticated) {
        setMatches(mockMatches);
        setLoading(false);
        return;
      }

      try {
        const data = await matchesAPI.getMatches();
        if (data && data.length > 0) {
          // Transform API data to match component format
          const transformedMatches = data.map((match) => ({
            id: match.id,
            company: isEmployer
              ? match.candidate?.name || 'Unknown'
              : match.job?.employer?.companyName || 'Unknown Company',
            companyLogo: isEmployer
              ? match.candidate?.photo
              : match.job?.employer?.companyLogo,
            lastMessage: match.messages?.[0]?.content || 'התחילו שיחה...',
            lastActive: 'היום',
            hasUnread: match.messages?.some((m) => !m.isRead) || false,
            isOnline: false,
            job: match.job,
            candidate: match.candidate,
          }));
          setMatches(transformedMatches);
        } else {
          setMatches(mockMatches);
        }
      } catch (err) {
        console.error('Error fetching matches:', err);
        setMatches(mockMatches);
      } finally {
        setLoading(false);
      }
    };

    fetchMatches();
  }, [isAuthenticated, isEmployer]);

  const filteredMatches = matches.filter((match) =>
    match.company.toLowerCase().includes(searchQuery.toLowerCase())
  );

  if (selectedMatch) {
    return (
      <ChatInterface
        match={selectedMatch}
        onBack={() => setSelectedMatch(null)}
      />
    );
  }

  return (
    <PageContainer>
      <Header>
        <BackButton onClick={() => navigate('/feed')}>
          <ArrowRight size={24} />
        </BackButton>
        <HeaderTitle>ההתאמות שלי</HeaderTitle>
        <Spacer />
      </Header>

      <SearchContainer>
        <SearchInput>
          <Search size={20} />
          <input
            type="text"
            placeholder="חיפוש..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
          />
        </SearchInput>
      </SearchContainer>

      {loading ? (
        <LoadingContainer>
          <Loader size={40} />
          <p>טוען התאמות...</p>
        </LoadingContainer>
      ) : matches.length === 0 ? (
        <EmptyState>
          <MessageCircle size={48} />
          <h2>אין התאמות עדיין</h2>
          <p>המשיכו לעשות swipe על משרות - כשתהיה התאמה, תוכלו להתחיל לדבר!</p>
        </EmptyState>
      ) : (
        <>
          <MatchesScrollContainer>
            <SectionTitle>התאמות חדשות</SectionTitle>
            <MatchesRow>
              {filteredMatches.map((match) => (
                <MatchAvatar key={match.id} onClick={() => setSelectedMatch(match)}>
                  <Avatar
                    src={match.companyLogo}
                    name={match.company}
                    size="lg"
                    status={match.isOnline ? 'online' : undefined}
                    bordered
                  />
                  <MatchName>{match.company}</MatchName>
                </MatchAvatar>
              ))}
            </MatchesRow>
          </MatchesScrollContainer>

          <ConversationsList>
            <div style={{ padding: '0 1.5rem' }}>
              <SectionTitle>שיחות</SectionTitle>
            </div>
            {filteredMatches.map((match) => (
              <ConversationItem key={match.id} onClick={() => setSelectedMatch(match)}>
                <Avatar
                  src={match.companyLogo}
                  name={match.company}
                  size="md"
                  status={match.isOnline ? 'online' : undefined}
                />
                <ConversationInfo>
                  <ConversationName>{match.company}</ConversationName>
                  <ConversationPreview>{match.lastMessage}</ConversationPreview>
                </ConversationInfo>
                <ConversationMeta>
                  <TimeStamp>{match.lastActive}</TimeStamp>
                  {match.hasUnread && <Badge.Count count={1} size="sm" />}
                </ConversationMeta>
              </ConversationItem>
            ))}
          </ConversationsList>
        </>
      )}
    </PageContainer>
  );
};

export default MatchesPage;

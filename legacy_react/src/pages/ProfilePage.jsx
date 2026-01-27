import React from 'react';
import { useNavigate } from 'react-router-dom';
import styled from 'styled-components';
import { ArrowRight, Settings, LogOut, Camera, Briefcase, MapPin, Banknote } from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';
import { Avatar, Button, Badge, Card } from '../components/ui';

const PageContainer = styled.div`
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: ${({ theme }) => theme.colors.background};
`;

const Header = styled.header`
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: ${({ theme }) => theme.spacing.md} ${({ theme }) => theme.spacing.lg};
  background: ${({ theme }) => theme.colors.surface};
  border-bottom: 1px solid ${({ theme }) => theme.colors.borderLight};
`;

const IconButton = styled.button`
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

const Content = styled.div`
  flex: 1;
  padding: ${({ theme }) => theme.spacing.lg};
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.lg};
  overflow-y: auto;
`;

const ProfileHeader = styled(Card)`
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: ${({ theme }) => theme.spacing.xl};
`;

const AvatarWrapper = styled.div`
  position: relative;
  margin-bottom: ${({ theme }) => theme.spacing.md};
`;

const EditAvatarButton = styled.button`
  position: absolute;
  bottom: 0;
  right: 0;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: ${({ theme }) => theme.colors.primary};
  color: white;
  border: 3px solid ${({ theme }) => theme.colors.surface};
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};

  &:hover {
    background: ${({ theme }) => theme.colors.primaryDark};
    transform: scale(1.05);
  }
`;

const ProfileName = styled.h2`
  font-size: ${({ theme }) => theme.typography.fontSizes['2xl']};
  font-weight: ${({ theme }) => theme.typography.fontWeights.bold};
  color: ${({ theme }) => theme.colors.secondary};
  margin-bottom: ${({ theme }) => theme.spacing.xs};
`;

const ProfileRole = styled.span`
  color: ${({ theme }) => theme.colors.textMuted};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
`;

const Section = styled(Card)`
  padding: ${({ theme }) => theme.spacing.lg};
`;

const SectionHeader = styled.div`
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: ${({ theme }) => theme.spacing.md};
`;

const SectionTitle = styled.h3`
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.secondary};
`;

const EditButton = styled.button`
  background: none;
  border: none;
  color: ${({ theme }) => theme.colors.primary};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  cursor: pointer;
  transition: color ${({ theme }) => theme.transitions.fast};

  &:hover {
    color: ${({ theme }) => theme.colors.primaryDark};
  }
`;

const BioText = styled.p`
  color: ${({ theme }) => theme.colors.textMuted};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  line-height: ${({ theme }) => theme.typography.lineHeights.relaxed};
`;

const PreferencesGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: ${({ theme }) => theme.spacing.md};
`;

const PreferenceItem = styled.div`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.sm};
  padding: ${({ theme }) => theme.spacing.md};
  background: ${({ theme }) => theme.colors.background};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
`;

const PreferenceIcon = styled.div`
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: ${({ theme }) => theme.colors.primaryLight};
  color: ${({ theme }) => theme.colors.primary};
  border-radius: ${({ theme }) => theme.borderRadius.md};
`;

const PreferenceContent = styled.div`
  flex: 1;
`;

const PreferenceLabel = styled.span`
  display: block;
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme }) => theme.colors.textMuted};
  margin-bottom: 2px;
`;

const PreferenceValue = styled.span`
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.secondary};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
`;

const LogoutButtonStyled = styled(Button)`
  margin-top: auto;
`;

const ProfilePage = () => {
  const navigate = useNavigate();
  const { user, logout } = useAuth();

  // Fallback user data for development
  const userData = user || {
    name: 'משתמש לדוגמה',
    bio: 'מפתח פול-סטאק שאוהב לבנות מוצרים. מחפש את האתגר הבא בעולמות ה-WEB.',
    photo: null,
    field: 'dev',
    salary: 22000,
    workModel: 'hybrid',
    role: 'CANDIDATE',
  };

  const getFieldLabel = (field) => {
    const fields = {
      dev: 'פיתוח תוכנה',
      design: 'עיצוב',
      marketing: 'שיווק',
      sales: 'מכירות',
      admin: 'אדמיניסטרציה',
    };
    return fields[field] || field || 'לא הוגדר';
  };

  const getWorkModelLabel = (model) => {
    const models = {
      office: 'עבודה מהמשרד',
      hybrid: 'היברידי',
      remote: 'עבודה מהבית',
    };
    return models[model] || model || 'לא הוגדר';
  };

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  return (
    <PageContainer>
      <Header>
        <IconButton onClick={() => navigate('/feed')}>
          <ArrowRight size={24} />
        </IconButton>
        <HeaderTitle>הפרופיל שלי</HeaderTitle>
        <IconButton onClick={() => navigate('/settings')}>
          <Settings size={24} />
        </IconButton>
      </Header>

      <Content>
        <ProfileHeader padding={false}>
          <AvatarWrapper>
            <Avatar
              src={userData.photo}
              name={userData.name}
              size="xxl"
            />
            <EditAvatarButton>
              <Camera size={16} />
            </EditAvatarButton>
          </AvatarWrapper>
          <ProfileName>{userData.name || 'ללא שם'}</ProfileName>
          <ProfileRole>{getFieldLabel(userData.field)}</ProfileRole>
          {userData.role === 'CANDIDATE' && (
            <Badge variant="primary" style={{ marginTop: '0.5rem' }}>
              מחפש/ת עבודה
            </Badge>
          )}
        </ProfileHeader>

        <Section padding={false}>
          <SectionHeader>
            <SectionTitle>אודותיי</SectionTitle>
            <EditButton>עריכה</EditButton>
          </SectionHeader>
          <BioText>
            {userData.bio || 'לא הוזן תיאור עדיין. לחצו על עריכה כדי להוסיף.'}
          </BioText>
        </Section>

        <Section padding={false}>
          <SectionHeader>
            <SectionTitle>העדפות</SectionTitle>
            <EditButton>עריכה</EditButton>
          </SectionHeader>
          <PreferencesGrid>
            <PreferenceItem>
              <PreferenceIcon>
                <Banknote size={20} />
              </PreferenceIcon>
              <PreferenceContent>
                <PreferenceLabel>שכר ציפייה</PreferenceLabel>
                <PreferenceValue>
                  {userData.salary
                    ? `${parseInt(userData.salary).toLocaleString()}₪`
                    : 'לא הוגדר'}
                </PreferenceValue>
              </PreferenceContent>
            </PreferenceItem>

            <PreferenceItem>
              <PreferenceIcon>
                <MapPin size={20} />
              </PreferenceIcon>
              <PreferenceContent>
                <PreferenceLabel>מודל עבודה</PreferenceLabel>
                <PreferenceValue>
                  {getWorkModelLabel(userData.workModel)}
                </PreferenceValue>
              </PreferenceContent>
            </PreferenceItem>

            <PreferenceItem>
              <PreferenceIcon>
                <Briefcase size={20} />
              </PreferenceIcon>
              <PreferenceContent>
                <PreferenceLabel>תחום</PreferenceLabel>
                <PreferenceValue>
                  {getFieldLabel(userData.field)}
                </PreferenceValue>
              </PreferenceContent>
            </PreferenceItem>
          </PreferencesGrid>
        </Section>

        <LogoutButtonStyled variant="danger" fullWidth onClick={handleLogout}>
          <LogOut size={20} />
          התנתקות
        </LogoutButtonStyled>
      </Content>
    </PageContainer>
  );
};

export default ProfilePage;

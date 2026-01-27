import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import styled from 'styled-components';
import { Mail, Lock, ArrowLeft, Briefcase, Search } from 'lucide-react';
import { useAuth } from '../contexts/AuthContext';
import { Button, Input } from '../components/ui';
import logo from '../assets/LOGO.jpeg';

const PageContainer = styled.div`
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: ${({ theme }) => theme.colors.background};
  padding: ${({ theme }) => theme.spacing.md};

  @media (min-width: 640px) {
    padding: ${({ theme }) => theme.spacing.xl};
  }
`;

const BackButton = styled(Link)`
  display: inline-flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  color: ${({ theme }) => theme.colors.textMuted};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  text-decoration: none;
  margin-bottom: ${({ theme }) => theme.spacing.md};
  transition: color ${({ theme }) => theme.transitions.fast};
  align-self: flex-start;

  &:hover {
    color: ${({ theme }) => theme.colors.primary};
  }
`;

const Content = styled.div`
  flex: 1;
  display: flex;
  flex-direction: column;
  max-width: 400px;
  margin: 0 auto;
  width: 100%;
`;

const LogoContainer = styled.div`
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: ${({ theme }) => theme.spacing.lg};
`;

const Logo = styled.img`
  width: 56px;
  height: 56px;
  border-radius: ${({ theme }) => theme.borderRadius.md};
  box-shadow: ${({ theme }) => theme.shadows.sm};
  margin-bottom: ${({ theme }) => theme.spacing.sm};
`;

const Title = styled.h1`
  font-size: ${({ theme }) => theme.typography.fontSizes.xl};
  font-weight: ${({ theme }) => theme.typography.fontWeights.bold};
  color: ${({ theme }) => theme.colors.secondary};
  margin-bottom: ${({ theme }) => theme.spacing.xs};
  text-align: center;
`;

const Subtitle = styled.p`
  color: ${({ theme }) => theme.colors.textMuted};
  text-align: center;
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
`;

const RoleSelector = styled.div`
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: ${({ theme }) => theme.spacing.sm};
  margin-bottom: ${({ theme }) => theme.spacing.lg};
`;

const RoleCard = styled.button`
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  padding: ${({ theme }) => theme.spacing.md};
  background-color: ${({ theme, $selected }) =>
    $selected ? theme.colors.primaryLight : theme.colors.surface};
  border: 2px solid
    ${({ theme, $selected }) =>
      $selected ? theme.colors.primary : theme.colors.border};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};

  &:hover {
    border-color: ${({ theme }) => theme.colors.primary};
  }
`;

const RoleIcon = styled.div`
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: ${({ theme, $selected }) =>
    $selected ? theme.colors.primary : theme.colors.background};
  color: ${({ theme, $selected }) =>
    $selected ? 'white' : theme.colors.textMuted};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  transition: all ${({ theme }) => theme.transitions.fast};
`;

const RoleTitle = styled.span`
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme, $selected }) =>
    $selected ? theme.colors.primary : theme.colors.secondary};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
`;

const RoleDescription = styled.span`
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme }) => theme.colors.textMuted};
  text-align: center;
  line-height: 1.3;
`;

const Form = styled.form`
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.sm};
`;

const Terms = styled.p`
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme }) => theme.colors.textMuted};
  text-align: center;
  margin-top: ${({ theme }) => theme.spacing.sm};
  line-height: 1.5;

  a {
    color: ${({ theme }) => theme.colors.primary};

    &:hover {
      text-decoration: underline;
    }
  }
`;

const LoginLink = styled.p`
  text-align: center;
  color: ${({ theme }) => theme.colors.textMuted};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  margin-top: ${({ theme }) => theme.spacing.md};

  a {
    color: ${({ theme }) => theme.colors.primary};
    font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};

    &:hover {
      color: ${({ theme }) => theme.colors.primaryDark};
    }
  }
`;

const ErrorMessage = styled.div`
  background-color: ${({ theme }) => theme.colors.errorLight};
  color: ${({ theme }) => theme.colors.error};
  padding: ${({ theme }) => theme.spacing.sm} ${({ theme }) => theme.spacing.md};
  border-radius: ${({ theme }) => theme.borderRadius.sm};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  text-align: center;
  border: 1px solid ${({ theme }) => theme.colors.error}20;
`;

const RegisterPage = () => {
  const [role, setRole] = useState('CANDIDATE');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [error, setError] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const { register } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    // Validation
    if (!email || !password || !confirmPassword) {
      setError('Please fill in all fields');
      return;
    }

    if (password !== confirmPassword) {
      setError('Passwords do not match');
      return;
    }

    if (password.length < 6) {
      setError('Password must be at least 6 characters');
      return;
    }

    setIsLoading(true);

    try {
      const result = await register({ email, password, role });

      if (result.success) {
        // Navigate to appropriate onboarding
        const onboardingPath =
          role === 'EMPLOYER' ? '/onboarding/employer' : '/onboarding';
        navigate(onboardingPath);
      } else {
        setError(result.error || 'Registration failed. Please try again.');
      }
    } catch (err) {
      setError('An unexpected error occurred. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <PageContainer>
      <BackButton to="/">
        <ArrowLeft size={18} />
        חזרה לדף הבית
      </BackButton>

      <Content>
        <LogoContainer>
          <Logo src={logo} alt="Matcha Logo" />
          <Title>יצירת חשבון חדש</Title>
          <Subtitle>הצטרפו לקהילת המשתמשים שלנו</Subtitle>
        </LogoContainer>

        <RoleSelector>
          <RoleCard
            type="button"
            $selected={role === 'CANDIDATE'}
            onClick={() => setRole('CANDIDATE')}
          >
            <RoleIcon $selected={role === 'CANDIDATE'}>
              <Search size={24} />
            </RoleIcon>
            <RoleTitle $selected={role === 'CANDIDATE'}>מחפש/ת עבודה</RoleTitle>
            <RoleDescription>מצאו את המשרה המושלמת</RoleDescription>
          </RoleCard>

          <RoleCard
            type="button"
            $selected={role === 'EMPLOYER'}
            onClick={() => setRole('EMPLOYER')}
          >
            <RoleIcon $selected={role === 'EMPLOYER'}>
              <Briefcase size={24} />
            </RoleIcon>
            <RoleTitle $selected={role === 'EMPLOYER'}>מעסיק/ה</RoleTitle>
            <RoleDescription>גייסו עובדים מוכשרים</RoleDescription>
          </RoleCard>
        </RoleSelector>

        <Form onSubmit={handleSubmit}>
          {error && <ErrorMessage>{error}</ErrorMessage>}

          <Input
            label="אימייל"
            type="email"
            placeholder="your@email.com"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            icon={<Mail size={18} />}
            autoComplete="email"
            required
          />

          <Input
            label="סיסמה"
            type="password"
            placeholder="לפחות 6 תווים"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            icon={<Lock size={18} />}
            autoComplete="new-password"
            required
          />

          <Input
            label="אישור סיסמה"
            type="password"
            placeholder="הזינו שוב את הסיסמה"
            value={confirmPassword}
            onChange={(e) => setConfirmPassword(e.target.value)}
            icon={<Lock size={18} />}
            autoComplete="new-password"
            required
            error={
              confirmPassword && password !== confirmPassword
                ? 'הסיסמאות אינן תואמות'
                : undefined
            }
          />

          <Button type="submit" fullWidth loading={isLoading}>
            {role === 'EMPLOYER' ? 'המשך להרשמת מעסיק' : 'המשך להרשמה'}
          </Button>
        </Form>

        <Terms>
          בהרשמה אתם מסכימים ל
          <Link to="/terms">תנאי השימוש</Link> ול
          <Link to="/privacy">מדיניות הפרטיות</Link> שלנו
        </Terms>

        <LoginLink>
          כבר יש לכם חשבון? <Link to="/login">התחברו</Link>
        </LoginLink>
      </Content>
    </PageContainer>
  );
};

export default RegisterPage;

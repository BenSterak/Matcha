import React from 'react';
import { Link } from 'react-router-dom';
import styled, { keyframes } from 'styled-components';
import { ArrowLeft, Sparkles, Users, Briefcase } from 'lucide-react';
import { Button } from '../components/ui';
import logo from '../assets/LOGO.jpeg';

const fadeIn = keyframes`
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
`;

const float = keyframes`
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-10px);
  }
`;

const PageContainer = styled.div`
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: linear-gradient(
    180deg,
    ${({ theme }) => theme.colors.primaryLight} 0%,
    ${({ theme }) => theme.colors.surface} 40%,
    ${({ theme }) => theme.colors.background} 100%
  );
  position: relative;
  overflow: hidden;
`;

const BackgroundDecor = styled.div`
  position: absolute;
  top: -100px;
  right: -100px;
  width: 300px;
  height: 300px;
  background: ${({ theme }) => theme.colors.primaryGlow};
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
`;

const BackgroundDecor2 = styled.div`
  position: absolute;
  bottom: -50px;
  left: -100px;
  width: 250px;
  height: 250px;
  background: ${({ theme }) => theme.colors.primaryGlow};
  border-radius: 50%;
  filter: blur(60px);
  pointer-events: none;
`;

const Content = styled.div`
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: ${({ theme }) => theme.spacing.xl};
  position: relative;
  z-index: 1;
`;

const LogoContainer = styled.div`
  animation: ${fadeIn} 0.8s ease-out, ${float} 4s ease-in-out infinite;
  margin-bottom: ${({ theme }) => theme.spacing.xl};
`;

const Logo = styled.img`
  width: 120px;
  height: 120px;
  border-radius: ${({ theme }) => theme.borderRadius.xxl};
  box-shadow: ${({ theme }) => theme.shadows.xxl};
`;

const Title = styled.h1`
  font-size: ${({ theme }) => theme.typography.fontSizes['5xl']};
  font-weight: ${({ theme }) => theme.typography.fontWeights.extrabold};
  color: ${({ theme }) => theme.colors.secondary};
  margin-bottom: ${({ theme }) => theme.spacing.sm};
  animation: ${fadeIn} 0.8s ease-out 0.2s both;
  letter-spacing: -0.02em;
`;

const Tagline = styled.p`
  font-size: ${({ theme }) => theme.typography.fontSizes.lg};
  color: ${({ theme }) => theme.colors.textMuted};
  text-align: center;
  max-width: 280px;
  animation: ${fadeIn} 0.8s ease-out 0.4s both;
  line-height: ${({ theme }) => theme.typography.lineHeights.relaxed};
`;

const FeaturesContainer = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.lg};
  margin: ${({ theme }) => theme.spacing.xxl} 0;
  animation: ${fadeIn} 0.8s ease-out 0.6s both;
`;

const Feature = styled.div`
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
`;

const FeatureIcon = styled.div`
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: ${({ theme }) => theme.colors.surface};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  box-shadow: ${({ theme }) => theme.shadows.md};
  color: ${({ theme }) => theme.colors.primary};
`;

const FeatureText = styled.span`
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme }) => theme.colors.textMuted};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
`;

const ActionsContainer = styled.div`
  width: 100%;
  max-width: 320px;
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.md};
  animation: ${fadeIn} 0.8s ease-out 0.8s both;
`;

const LoginLink = styled(Link)`
  text-align: center;
  color: ${({ theme }) => theme.colors.textMuted};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  transition: color ${({ theme }) => theme.transitions.fast};

  &:hover {
    color: ${({ theme }) => theme.colors.primary};
  }
`;

const Footer = styled.footer`
  padding: ${({ theme }) => theme.spacing.lg};
  text-align: center;
  animation: ${fadeIn} 0.8s ease-out 1s both;
`;

const FooterText = styled.p`
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme }) => theme.colors.textLight};
`;

const WelcomePage = () => {
  return (
    <PageContainer>
      <BackgroundDecor />
      <BackgroundDecor2 />

      <Content>
        <LogoContainer>
          <Logo src={logo} alt="Matcha Logo" />
        </LogoContainer>

        <Title>Matcha</Title>
        <Tagline>המשחק החדש של עולם הגיוס - מצאו את ההתאמה המושלמת</Tagline>

        <FeaturesContainer>
          <Feature>
            <FeatureIcon>
              <Sparkles size={22} />
            </FeatureIcon>
            <FeatureText>התאמה חכמה</FeatureText>
          </Feature>
          <Feature>
            <FeatureIcon>
              <Users size={22} />
            </FeatureIcon>
            <FeatureText>קשר ישיר</FeatureText>
          </Feature>
          <Feature>
            <FeatureIcon>
              <Briefcase size={22} />
            </FeatureIcon>
            <FeatureText>משרות איכותיות</FeatureText>
          </Feature>
        </FeaturesContainer>

        <ActionsContainer>
          <Button as={Link} to="/register" fullWidth size="lg">
            בואו נתחיל
            <ArrowLeft size={20} />
          </Button>
          <LoginLink to="/login">יש לי כבר חשבון</LoginLink>
        </ActionsContainer>
      </Content>

      <Footer>
        <FooterText>Matcha 2024 - כל הזכויות שמורות</FooterText>
      </Footer>
    </PageContainer>
  );
};

export default WelcomePage;

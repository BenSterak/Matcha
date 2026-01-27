import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import styled from 'styled-components';
import { ArrowRight, ArrowLeft, Check } from 'lucide-react';
import { useAuth } from '../../../contexts/AuthContext';
import { authAPI } from '../../../services/api';
import { Button } from '../../ui';
import StepProfile from './StepProfile';
import StepPreferences from './StepPreferences';

const Container = styled.div`
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: ${({ theme }) => theme.colors.surface};
`;

const ProgressBar = styled.div`
  height: 4px;
  background-color: ${({ theme }) => theme.colors.borderLight};
  width: 100%;
`;

const ProgressFill = styled.div`
  height: 100%;
  background-color: ${({ theme }) => theme.colors.primary};
  transition: width 0.3s ease;
  width: ${({ $progress }) => $progress}%;
`;

const StepsIndicator = styled.div`
  display: flex;
  justify-content: center;
  gap: ${({ theme }) => theme.spacing.lg};
  padding: ${({ theme }) => theme.spacing.lg};
`;

const StepDot = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  transition: all ${({ theme }) => theme.transitions.normal};

  ${({ $active, $completed, theme }) =>
    $completed
      ? `
        background-color: ${theme.colors.primary};
        color: white;
      `
      : $active
        ? `
        background-color: ${theme.colors.primary};
        color: white;
      `
        : `
        background-color: ${theme.colors.background};
        color: ${theme.colors.textMuted};
      `}
`;

const Content = styled.div`
  flex: 1;
  padding: ${({ theme }) => theme.spacing.lg};
  overflow-y: auto;
`;

const Footer = styled.div`
  padding: ${({ theme }) => theme.spacing.lg} ${({ theme }) => theme.spacing.xl};
  background-color: ${({ theme }) => theme.colors.surface};
  border-top: 1px solid ${({ theme }) => theme.colors.borderLight};
  display: flex;
  justify-content: space-between;
  align-items: center;
`;

const BackButton = styled.button`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  padding: ${({ theme }) => theme.spacing.sm} ${({ theme }) => theme.spacing.md};
  background: none;
  border: none;
  color: ${({ theme }) => theme.colors.textMuted};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  cursor: pointer;
  transition: color ${({ theme }) => theme.transitions.fast};

  &:hover {
    color: ${({ theme }) => theme.colors.text};
  }
`;

const OnboardingWizard = () => {
  const [step, setStep] = useState(0);
  const [isLoading, setIsLoading] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    photo: null,
    bio: '',
    field: '',
    salary: 12000,
    workModel: 'hybrid',
  });

  const navigate = useNavigate();
  const { updateProfile, updateUserLocal } = useAuth();

  const steps = [
    { component: StepProfile, validate: (data) => data.name && data.name.length > 2 },
    { component: StepPreferences, validate: (data) => !!data.field },
  ];

  const CurrentStepComponent = steps[step].component;
  const isStepValid = steps[step].validate(formData);
  const progress = ((step + 1) / steps.length) * 100;

  const handleNext = async () => {
    if (step < steps.length - 1) {
      setStep(step + 1);
    } else {
      // Final step - save profile
      setIsLoading(true);
      try {
        const result = await updateProfile({
          ...formData,
          onboardingComplete: true,
        });

        if (result.success) {
          navigate('/feed');
        } else {
          // Show error to user
          console.error('Profile update failed:', result.error);
          alert(`שגיאה בשמירת הפרופיל: ${result.error || 'אנא נסה שנית'}`);
          setIsLoading(false);
        }
      } catch (error) {
        console.error('Error saving profile:', error);
        alert(`שגיאה לא צפויה: ${error.message}`);
        setIsLoading(false);
      } finally {
        // setIsLoading(false); // Already handled
      }
    }
  };

  const handleBack = () => {
    if (step > 0) {
      setStep(step - 1);
    }
  };

  return (
    <Container>
      <ProgressBar>
        <ProgressFill $progress={progress} />
      </ProgressBar>

      <StepsIndicator>
        {steps.map((_, index) => (
          <StepDot
            key={index}
            $active={step === index}
            $completed={step > index}
          >
            {step > index ? <Check size={16} /> : index + 1}
          </StepDot>
        ))}
      </StepsIndicator>

      <Content>
        <CurrentStepComponent
          data={formData}
          onUpdate={setFormData}
        />
      </Content>

      <Footer>
        {step > 0 ? (
          <BackButton onClick={handleBack}>
            <ArrowRight size={20} />
            חזרה
          </BackButton>
        ) : (
          <div />
        )}

        <Button
          onClick={handleNext}
          disabled={!isStepValid}
          loading={isLoading}
          icon={step < steps.length - 1 ? <ArrowLeft size={20} /> : undefined}
          iconPosition="end"
        >
          {step === steps.length - 1 ? 'סיום' : 'המשך'}
        </Button>
      </Footer>
    </Container>
  );
};

export default OnboardingWizard;

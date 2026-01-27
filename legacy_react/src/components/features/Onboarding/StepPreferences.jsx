import React from 'react';
import styled from 'styled-components';
import { Briefcase, DollarSign, Home, MapPin } from 'lucide-react';

const Container = styled.div`
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.xl};
`;

const Header = styled.div`
  h2 {
    font-size: ${({ theme }) => theme.typography.fontSizes['2xl']};
    color: ${({ theme }) => theme.colors.secondary};
    margin-bottom: ${({ theme }) => theme.spacing.xs};
  }

  p {
    color: ${({ theme }) => theme.colors.textMuted};
  }
`;

const FormGroup = styled.div`
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.sm};
`;

const Label = styled.label`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.secondary};
`;

const SelectField = styled.select`
  width: 100%;
  padding: ${({ theme }) => theme.spacing.md};
  border: 1px solid ${({ theme }) => theme.colors.borderLight};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  background-color: white;
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: left 1rem center;
  background-size: 1em;
  cursor: pointer;
  transition: border-color ${({ theme }) => theme.transitions.fast},
              box-shadow ${({ theme }) => theme.transitions.fast};

  &:focus {
    outline: none;
    border-color: ${({ theme }) => theme.colors.primary};
    box-shadow: 0 0 0 3px ${({ theme }) => theme.colors.primaryGlow};
  }
`;

const SalarySliderContainer = styled.div`
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.md};
`;

const SalarySlider = styled.input`
  width: 100%;
  height: 6px;
  background: ${({ theme }) => theme.colors.borderLight};
  border-radius: ${({ theme }) => theme.borderRadius.sm};
  appearance: none;
  outline: none;
  cursor: pointer;

  &::-webkit-slider-thumb {
    appearance: none;
    width: 24px;
    height: 24px;
    background: ${({ theme }) => theme.colors.primary};
    border-radius: ${({ theme }) => theme.borderRadius.full};
    cursor: pointer;
    box-shadow: ${({ theme }) => theme.shadows.md};
    transition: transform ${({ theme }) => theme.transitions.fast};
  }

  &::-webkit-slider-thumb:hover {
    transform: scale(1.1);
  }

  &::-moz-range-thumb {
    width: 24px;
    height: 24px;
    background: ${({ theme }) => theme.colors.primary};
    border-radius: ${({ theme }) => theme.borderRadius.full};
    cursor: pointer;
    border: none;
    box-shadow: ${({ theme }) => theme.shadows.md};
  }
`;

const SalaryDisplay = styled.div`
  align-self: center;
  font-size: ${({ theme }) => theme.typography.fontSizes['2xl']};
  font-weight: ${({ theme }) => theme.typography.fontWeights.bold};
  color: ${({ theme }) => theme.colors.primaryDark};
`;

const ChipsContainer = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.sm};
`;

const Chip = styled.button`
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  padding: ${({ theme }) => theme.spacing.md};
  border: 1px solid ${({ $active, theme }) =>
    $active ? theme.colors.primary : theme.colors.borderLight};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  background: ${({ $active, theme }) =>
    $active ? theme.colors.primaryLight : 'white'};
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};
  color: ${({ $active, theme }) =>
    $active ? theme.colors.primaryDark : theme.colors.textMuted};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};

  ${({ $active, theme }) => $active && `
    box-shadow: 0 4px 6px -1px ${theme.colors.primaryGlow};
  `}

  &:hover {
    border-color: ${({ theme }) => theme.colors.primary};
  }
`;

const workModels = [
  { id: 'office', label: 'משרד', icon: Briefcase },
  { id: 'hybrid', label: 'היברידי', icon: MapPin },
  { id: 'remote', label: 'מהבית', icon: Home },
];

const StepPreferences = ({ data, onUpdate }) => {
  const handleChange = (field, value) => {
    onUpdate({ ...data, [field]: value });
  };

  return (
    <Container>
      <Header>
        <h2>מה אנחנו מחפשים?</h2>
        <p>נאמר למערכת מה מעניין אותך כדי למצוא התאמות מדויקות.</p>
      </Header>

      <FormGroup>
        <Label>
          <Briefcase size={18} />
          תחום עיסוק
        </Label>
        <SelectField
          value={data.field || ''}
          onChange={(e) => handleChange('field', e.target.value)}
        >
          <option value="" disabled>בחירת תחום...</option>
          <option value="dev">פיתוח תוכנה</option>
          <option value="design">עיצוב גרפי / UI/UX</option>
          <option value="marketing">שיווק ודיגיטל</option>
          <option value="sales">מכירות</option>
          <option value="admin">אדמיניסטרציה</option>
        </SelectField>
      </FormGroup>

      <FormGroup>
        <Label>
          <DollarSign size={18} />
          ציפיות שכר (חודשי)
        </Label>
        <SalarySliderContainer>
          <SalarySlider
            type="range"
            min="6000"
            max="40000"
            step="500"
            value={data.salary || 12000}
            onChange={(e) => handleChange('salary', e.target.value)}
          />
          <SalaryDisplay>
            ₪{parseInt(data.salary || 12000).toLocaleString()}
          </SalaryDisplay>
        </SalarySliderContainer>
      </FormGroup>

      <FormGroup>
        <Label>
          <Home size={18} />
          מודל עבודה
        </Label>
        <ChipsContainer>
          {workModels.map((model) => {
            const Icon = model.icon;
            return (
              <Chip
                key={model.id}
                type="button"
                $active={data.workModel === model.id}
                onClick={() => handleChange('workModel', model.id)}
              >
                <Icon size={20} />
                {model.label}
              </Chip>
            );
          })}
        </ChipsContainer>
      </FormGroup>
    </Container>
  );
};

export default StepPreferences;

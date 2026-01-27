import React from 'react';
import styled from 'styled-components';
import { Users, MapPin, FileText } from 'lucide-react';

const Container = styled.div`
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.lg};
  padding-bottom: ${({ theme }) => theme.spacing.xl};
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
  gap: ${({ theme }) => theme.spacing.xs};
`;

const Label = styled.label`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.secondary};
`;

const ChipsContainer = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.sm};
  flex-wrap: wrap;
`;

const Chip = styled.button`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  padding: ${({ theme }) => theme.spacing.sm} ${({ theme }) => theme.spacing.md};
  border: 1px solid ${({ $active, theme }) =>
    $active ? theme.colors.primary : theme.colors.borderLight};
  border-radius: ${({ theme }) => theme.borderRadius.full};
  background: ${({ $active, theme }) =>
    $active ? theme.colors.primaryLight : 'white'};
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};
  color: ${({ $active, theme }) =>
    $active ? theme.colors.primaryDark : theme.colors.textMuted};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};

  ${({ $active, theme }) => $active && `
    box-shadow: 0 2px 4px -1px ${theme.colors.primaryGlow};
  `}

  &:hover {
    border-color: ${({ theme }) => theme.colors.primary};
  }
`;

const InputField = styled.input`
  padding: ${({ theme }) => theme.spacing.md};
  border: 1px solid ${({ theme }) => theme.colors.borderLight};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-family: inherit;
  background-color: white;
  transition: border-color ${({ theme }) => theme.transitions.fast},
              box-shadow ${({ theme }) => theme.transitions.fast};

  &:focus {
    outline: none;
    border-color: ${({ theme }) => theme.colors.primary};
    box-shadow: 0 0 0 3px ${({ theme }) => theme.colors.primaryGlow};
  }
`;

const TextArea = styled.textarea`
  padding: ${({ theme }) => theme.spacing.md};
  border: 1px solid ${({ theme }) => theme.colors.borderLight};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-family: inherit;
  background-color: white;
  resize: none;
  transition: border-color ${({ theme }) => theme.transitions.fast},
              box-shadow ${({ theme }) => theme.transitions.fast};

  &:focus {
    outline: none;
    border-color: ${({ theme }) => theme.colors.primary};
    box-shadow: 0 0 0 3px ${({ theme }) => theme.colors.primaryGlow};
  }
`;

const CharCount = styled.span`
  align-self: flex-end;
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme }) => theme.colors.textMuted};
`;

const companySizes = [
  { id: '1-10', label: '1-10' },
  { id: '11-50', label: '11-50' },
  { id: '51-200', label: '51-200' },
  { id: '201-500', label: '201-500' },
  { id: '500+', label: '500+' },
];

const StepCompanyDetails = ({ data, onUpdate }) => {
  const handleChange = (field, value) => {
    onUpdate({ ...data, [field]: value });
  };

  return (
    <Container>
      <Header>
        <h2>עוד קצת פרטים</h2>
        <p>מידע נוסף שיעזור למועמדים להכיר את החברה.</p>
      </Header>

      <FormGroup>
        <Label>
          <Users size={18} />
          גודל החברה (עובדים)
        </Label>
        <ChipsContainer>
          {companySizes.map((size) => (
            <Chip
              key={size.id}
              type="button"
              $active={data.companySize === size.id}
              onClick={() => handleChange('companySize', size.id)}
            >
              {size.label}
            </Chip>
          ))}
        </ChipsContainer>
      </FormGroup>

      <FormGroup>
        <Label>
          <MapPin size={18} />
          מיקום משרדים
        </Label>
        <InputField
          type="text"
          value={data.location || ''}
          onChange={(e) => handleChange('location', e.target.value)}
          placeholder="תל אביב, רמת גן..."
        />
      </FormGroup>

      <FormGroup>
        <Label>
          <FileText size={18} />
          תיאור החברה
        </Label>
        <TextArea
          value={data.companyBio || ''}
          onChange={(e) => handleChange('companyBio', e.target.value)}
          placeholder="ספרו על החברה, התרבות, והערכים שלכם..."
          rows={5}
          maxLength={300}
        />
        <CharCount>{data.companyBio?.length || 0}/300</CharCount>
      </FormGroup>
    </Container>
  );
};

export default StepCompanyDetails;

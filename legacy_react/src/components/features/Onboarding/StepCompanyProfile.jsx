import React from 'react';
import styled from 'styled-components';
import { Camera, Building2, Factory } from 'lucide-react';

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

const LogoUpload = styled.div`
  display: flex;
  justify-content: center;
  margin: ${({ theme }) => theme.spacing.md} 0;
`;

const LogoLabel = styled.label`
  cursor: pointer;
  transition: transform ${({ theme }) => theme.transitions.fast};

  &:active {
    transform: scale(0.95);
  }
`;

const LogoPlaceholder = styled.div`
  width: 120px;
  height: 120px;
  border-radius: ${({ theme }) => theme.borderRadius.xl};
  border: 2px dashed ${({ theme }) => theme.colors.primary};
  background-color: ${({ theme }) => theme.colors.background};
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: ${({ theme }) => theme.spacing.xs};
  color: ${({ theme }) => theme.colors.primary};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
`;

const LogoPreview = styled.img`
  width: 120px;
  height: 120px;
  border-radius: ${({ theme }) => theme.borderRadius.xl};
  object-fit: cover;
  box-shadow: ${({ theme }) => theme.shadows.lg};
  border: 3px solid ${({ theme }) => theme.colors.primary};
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

const SelectField = styled.select`
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

const HiddenInput = styled.input`
  display: none;
`;

const StepCompanyProfile = ({ data, onUpdate }) => {
  const handleChange = (field, value) => {
    onUpdate({ ...data, [field]: value });
  };

  const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        handleChange('companyLogo', reader.result);
      };
      reader.readAsDataURL(file);
    }
  };

  return (
    <Container>
      <Header>
        <h2>פרטי החברה</h2>
        <p>ספרו למועמדים על החברה שלכם.</p>
      </Header>

      <LogoUpload>
        <LogoLabel htmlFor="logo-input">
          {data.companyLogo ? (
            <LogoPreview src={data.companyLogo} alt="Company Logo" />
          ) : (
            <LogoPlaceholder>
              <Camera size={32} />
              <span>לוגו החברה</span>
            </LogoPlaceholder>
          )}
        </LogoLabel>
        <HiddenInput
          id="logo-input"
          type="file"
          accept="image/*"
          onChange={handleImageUpload}
        />
      </LogoUpload>

      <FormGroup>
        <Label>
          <Building2 size={18} />
          שם החברה
        </Label>
        <InputField
          type="text"
          value={data.companyName || ''}
          onChange={(e) => handleChange('companyName', e.target.value)}
          placeholder="שם החברה שלכם"
        />
      </FormGroup>

      <FormGroup>
        <Label>
          <Factory size={18} />
          תעשייה
        </Label>
        <SelectField
          value={data.industry || ''}
          onChange={(e) => handleChange('industry', e.target.value)}
        >
          <option value="" disabled>בחירת תעשייה...</option>
          <option value="tech">טכנולוגיה</option>
          <option value="finance">פיננסים</option>
          <option value="healthcare">בריאות</option>
          <option value="education">חינוך</option>
          <option value="retail">קמעונאות</option>
          <option value="media">מדיה ופרסום</option>
          <option value="manufacturing">תעשייה</option>
          <option value="other">אחר</option>
        </SelectField>
      </FormGroup>
    </Container>
  );
};

export default StepCompanyProfile;

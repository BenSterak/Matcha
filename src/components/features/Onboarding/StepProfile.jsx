import React from 'react';
import styled from 'styled-components';
import { Camera, User, FileText } from 'lucide-react';

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

const PhotoUpload = styled.div`
  display: flex;
  justify-content: center;
  margin: ${({ theme }) => theme.spacing.md} 0;
`;

const PhotoLabel = styled.label`
  cursor: pointer;
  transition: transform ${({ theme }) => theme.transitions.fast};

  &:active {
    transform: scale(0.95);
  }
`;

const PhotoPlaceholder = styled.div`
  width: 120px;
  height: 120px;
  border-radius: ${({ theme }) => theme.borderRadius.full};
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

const ProfilePreview = styled.img`
  width: 120px;
  height: 120px;
  border-radius: ${({ theme }) => theme.borderRadius.full};
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

const HiddenInput = styled.input`
  display: none;
`;

const StepProfile = ({ data, onUpdate }) => {
  const handleChange = (field, value) => {
    onUpdate({ ...data, [field]: value });
  };

  const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        handleChange('photo', reader.result);
      };
      reader.readAsDataURL(file);
    }
  };

  return (
    <Container>
      <Header>
        <h2>מי אני?</h2>
        <p>בוא/י נכיר קצת, זה מה שהמעסיקים יראו.</p>
      </Header>

      <PhotoUpload>
        <PhotoLabel htmlFor="photo-input">
          {data.photo ? (
            <ProfilePreview src={data.photo} alt="Profile" />
          ) : (
            <PhotoPlaceholder>
              <Camera size={32} />
              <span>העלאת תמונה</span>
            </PhotoPlaceholder>
          )}
        </PhotoLabel>
        <HiddenInput
          id="photo-input"
          type="file"
          accept="image/*"
          onChange={handleImageUpload}
        />
      </PhotoUpload>

      <FormGroup>
        <Label>
          <User size={18} />
          שם מלא
        </Label>
        <InputField
          type="text"
          value={data.name || ''}
          onChange={(e) => handleChange('name', e.target.value)}
          placeholder="ישראל ישראלי"
        />
      </FormGroup>

      <FormGroup>
        <Label>
          <FileText size={18} />
          קצת עליי (Bio)
        </Label>
        <TextArea
          value={data.bio || ''}
          onChange={(e) => handleChange('bio', e.target.value)}
          placeholder="אני מפתח נלהב עם תשוקה ל..."
          rows={4}
          maxLength={150}
        />
        <CharCount>{data.bio?.length || 0}/150</CharCount>
      </FormGroup>
    </Container>
  );
};

export default StepProfile;

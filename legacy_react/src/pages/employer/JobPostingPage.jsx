import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import styled from 'styled-components';
import {
  ArrowRight,
  Briefcase,
  MapPin,
  DollarSign,
  FileText,
  Clock,
  Home,
  Building2,
  Laptop,
  Save,
  Trash2,
} from 'lucide-react';
import { useAuth } from '../../contexts/AuthContext';
import { jobsAPI } from '../../services/api';
import { Button, Input, Card } from '../../components/ui';

const PageContainer = styled.div`
  min-height: 100vh;
  background-color: ${({ theme }) => theme.colors.background};
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

const Content = styled.main`
  padding: ${({ theme }) => theme.spacing.lg};
  max-width: 600px;
  margin: 0 auto;
`;

const Section = styled.section`
  margin-bottom: ${({ theme }) => theme.spacing.xl};
`;

const SectionTitle = styled.h2`
  font-size: ${({ theme }) => theme.typography.fontSizes.lg};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.secondary};
  margin-bottom: ${({ theme }) => theme.spacing.md};
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.sm};
`;

const FormGroup = styled.div`
  margin-bottom: ${({ theme }) => theme.spacing.lg};
`;

const Label = styled.label`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  color: ${({ theme }) => theme.colors.text};
  margin-bottom: ${({ theme }) => theme.spacing.xs};
`;

const InputField = styled.input`
  width: 100%;
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
  width: 100%;
  padding: ${({ theme }) => theme.spacing.md};
  border: 1px solid ${({ theme }) => theme.colors.borderLight};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-family: inherit;
  background-color: white;
  resize: vertical;
  min-height: 120px;
  transition: border-color ${({ theme }) => theme.transitions.fast},
              box-shadow ${({ theme }) => theme.transitions.fast};

  &:focus {
    outline: none;
    border-color: ${({ theme }) => theme.colors.primary};
    box-shadow: 0 0 0 3px ${({ theme }) => theme.colors.primaryGlow};
  }
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

const SalaryRange = styled.div`
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.md};

  span {
    text-align: center;
    color: ${({ theme }) => theme.colors.textMuted};
  }
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

const Footer = styled.footer`
  position: sticky;
  bottom: 0;
  padding: ${({ theme }) => theme.spacing.lg};
  background: white;
  border-top: 1px solid ${({ theme }) => theme.colors.borderLight};
  display: flex;
  gap: ${({ theme }) => theme.spacing.md};
`;

const jobTypes = [
  { id: 'full-time', label: 'משרה מלאה', icon: Clock },
  { id: 'part-time', label: 'חלקית', icon: Clock },
  { id: 'contract', label: 'פרילנס', icon: FileText },
];

const workModels = [
  { id: 'office', label: 'משרד', icon: Building2 },
  { id: 'hybrid', label: 'היברידי', icon: Laptop },
  { id: 'remote', label: 'מהבית', icon: Home },
];

const JobPostingPage = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user } = useAuth();
  const isEditing = !!id;

  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    location: '',
    salaryMin: '',
    salaryMax: '',
    type: 'full-time',
    workModel: 'hybrid',
    requirements: '',
  });

  useEffect(() => {
    if (isEditing) {
      // Fetch job data for editing
      const fetchJob = async () => {
        try {
          const job = await jobsAPI.getById(id);
          if (job) {
            setFormData({
              title: job.title || '',
              description: job.description || '',
              location: job.location || '',
              salaryMin: job.salaryMin || '',
              salaryMax: job.salaryMax || '',
              type: job.type || 'full-time',
              workModel: job.workModel || 'hybrid',
              requirements: job.requirements || '',
            });
          }
        } catch (error) {
          console.error('Error fetching job:', error);
        }
      };
      fetchJob();
    }
  }, [id, isEditing]);

  const handleChange = (field, value) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handleSubmit = async () => {
    setLoading(true);
    try {
      if (isEditing) {
        await jobsAPI.update(id, formData);
      } else {
        await jobsAPI.create(formData);
      }
      navigate('/employer/jobs');
    } catch (error) {
      console.error('Error saving job:', error);
      // Navigate anyway for demo
      navigate('/employer/jobs');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async () => {
    if (window.confirm('האם אתה בטוח שברצונך למחוק את המשרה?')) {
      try {
        await jobsAPI.delete(id);
        navigate('/employer/jobs');
      } catch (error) {
        console.error('Error deleting job:', error);
      }
    }
  };

  const isValid = formData.title && formData.description && formData.location;

  return (
    <PageContainer>
      <Header>
        <BackButton onClick={() => navigate(-1)}>
          <ArrowRight size={24} />
        </BackButton>
        <HeaderTitle>{isEditing ? 'עריכת משרה' : 'פרסום משרה חדשה'}</HeaderTitle>
      </Header>

      <Content>
        <Section>
          <SectionTitle>
            <Briefcase size={20} />
            פרטי המשרה
          </SectionTitle>

          <FormGroup>
            <Label>כותרת המשרה</Label>
            <InputField
              type="text"
              value={formData.title}
              onChange={(e) => handleChange('title', e.target.value)}
              placeholder="למשל: Frontend Developer"
            />
          </FormGroup>

          <FormGroup>
            <Label>
              <MapPin size={16} />
              מיקום
            </Label>
            <InputField
              type="text"
              value={formData.location}
              onChange={(e) => handleChange('location', e.target.value)}
              placeholder="תל אביב, רמת גן..."
            />
          </FormGroup>

          <FormGroup>
            <Label>סוג משרה</Label>
            <ChipsContainer>
              {jobTypes.map((type) => {
                const Icon = type.icon;
                return (
                  <Chip
                    key={type.id}
                    type="button"
                    $active={formData.type === type.id}
                    onClick={() => handleChange('type', type.id)}
                  >
                    <Icon size={16} />
                    {type.label}
                  </Chip>
                );
              })}
            </ChipsContainer>
          </FormGroup>

          <FormGroup>
            <Label>מודל עבודה</Label>
            <ChipsContainer>
              {workModels.map((model) => {
                const Icon = model.icon;
                return (
                  <Chip
                    key={model.id}
                    type="button"
                    $active={formData.workModel === model.id}
                    onClick={() => handleChange('workModel', model.id)}
                  >
                    <Icon size={16} />
                    {model.label}
                  </Chip>
                );
              })}
            </ChipsContainer>
          </FormGroup>
        </Section>

        <Section>
          <SectionTitle>
            <DollarSign size={20} />
            שכר
          </SectionTitle>

          <FormGroup>
            <Label>טווח שכר חודשי (₪)</Label>
            <SalaryRange>
              <InputField
                type="number"
                value={formData.salaryMin}
                onChange={(e) => handleChange('salaryMin', e.target.value)}
                placeholder="מינימום"
              />
              <span>עד</span>
              <InputField
                type="number"
                value={formData.salaryMax}
                onChange={(e) => handleChange('salaryMax', e.target.value)}
                placeholder="מקסימום"
              />
            </SalaryRange>
          </FormGroup>
        </Section>

        <Section>
          <SectionTitle>
            <FileText size={20} />
            תיאור המשרה
          </SectionTitle>

          <FormGroup>
            <Label>תיאור התפקיד</Label>
            <TextArea
              value={formData.description}
              onChange={(e) => handleChange('description', e.target.value)}
              placeholder="תארו את התפקיד, האחריות והאתגרים..."
            />
          </FormGroup>

          <FormGroup>
            <Label>דרישות</Label>
            <TextArea
              value={formData.requirements}
              onChange={(e) => handleChange('requirements', e.target.value)}
              placeholder="ניסיון נדרש, השכלה, כישורים..."
            />
          </FormGroup>
        </Section>
      </Content>

      <Footer>
        {isEditing && (
          <Button
            variant="danger"
            icon={<Trash2 size={18} />}
            onClick={handleDelete}
          >
            מחיקה
          </Button>
        )}
        <Button
          fullWidth
          onClick={handleSubmit}
          disabled={!isValid}
          loading={loading}
          icon={<Save size={18} />}
        >
          {isEditing ? 'שמירת שינויים' : 'פרסום משרה'}
        </Button>
      </Footer>
    </PageContainer>
  );
};

export default JobPostingPage;

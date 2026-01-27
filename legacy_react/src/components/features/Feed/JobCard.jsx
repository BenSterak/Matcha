import React from 'react';
import styled from 'styled-components';
import { MapPin, Briefcase, Banknote } from 'lucide-react';

const CardContainer = styled.div`
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: white;
  border-radius: ${({ theme }) => theme.borderRadius.xl};
  box-shadow: ${({ theme }) => theme.shadows.xl};
  overflow: hidden;
  user-select: none;
  cursor: grab;
  transform-origin: 50% 100%;

  &:active {
    cursor: grabbing;
  }
`;

const ImageContainer = styled.div`
  position: relative;
  height: 45%;
  width: 100%;
`;

const CardImage = styled.img`
  width: 100%;
  height: 100%;
  object-fit: cover;
`;

const ImageOverlay = styled.div`
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: ${({ theme }) => theme.spacing.lg};
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  color: white;
`;

const JobTitle = styled.h2`
  font-size: ${({ theme }) => theme.typography.fontSizes.xl};
  font-weight: ${({ theme }) => theme.typography.fontWeights.bold};
  margin-bottom: ${({ theme }) => theme.spacing.xs};
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
`;

const CompanyName = styled.p`
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  opacity: 0.9;
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
`;

const CardContent = styled.div`
  padding: ${({ theme }) => theme.spacing.lg};
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.lg};
`;

const TagsContainer = styled.div`
  display: flex;
  gap: ${({ theme }) => theme.spacing.xs};
  flex-wrap: wrap;
`;

const Tag = styled.div`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
  padding: ${({ theme }) => theme.spacing.xs} ${({ theme }) => theme.spacing.sm};
  background-color: ${({ theme }) => theme.colors.background};
  border-radius: ${({ theme }) => theme.borderRadius.full};
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  color: ${({ theme }) => theme.colors.text};
`;

const JobDescription = styled.div`
  h3 {
    font-size: ${({ theme }) => theme.typography.fontSizes.md};
    font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
    margin-bottom: ${({ theme }) => theme.spacing.xs};
    color: ${({ theme }) => theme.colors.secondary};
  }

  p {
    font-size: ${({ theme }) => theme.typography.fontSizes.sm};
    color: ${({ theme }) => theme.colors.textMuted};
    line-height: ${({ theme }) => theme.typography.lineHeights.relaxed};
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
`;

const JobCard = ({ job, style }) => {
  return (
    <CardContainer style={style}>
      <ImageContainer>
        <CardImage src={job.image} alt={job.title} />
        <ImageOverlay>
          <JobTitle>{job.title}</JobTitle>
          <CompanyName>{job.company}</CompanyName>
        </ImageOverlay>
      </ImageContainer>

      <CardContent>
        <TagsContainer>
          <Tag>
            <Banknote size={14} />
            {job.salary}
          </Tag>
          <Tag>
            <MapPin size={14} />
            {job.location}
          </Tag>
          <Tag>
            <Briefcase size={14} />
            {job.type}
          </Tag>
        </TagsContainer>

        <JobDescription>
          <h3>על התפקיד</h3>
          <p>{job.description}</p>
        </JobDescription>
      </CardContent>
    </CardContainer>
  );
};

export default JobCard;

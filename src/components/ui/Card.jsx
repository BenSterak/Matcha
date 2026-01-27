import React from 'react';
import styled, { css } from 'styled-components';

const StyledCard = styled.div`
  background-color: ${({ theme }) => theme.colors.surface};
  border-radius: ${({ theme, $radius }) =>
    $radius ? theme.borderRadius[$radius] : theme.borderRadius.xl};
  overflow: hidden;
  transition: all ${({ theme }) => theme.transitions.normal};

  /* Variants */
  ${({ $variant, theme }) => {
    switch ($variant) {
      case 'outlined':
        return css`
          border: 2px solid ${theme.colors.border};
        `;
      case 'flat':
        return css`
          background-color: ${theme.colors.background};
        `;
      default: // elevated
        return css`
          box-shadow: ${theme.shadows.card};
        `;
    }
  }}

  /* Hover effect */
  ${({ $hoverable, theme }) =>
    $hoverable &&
    css`
      cursor: pointer;

      &:hover {
        transform: translateY(-4px);
        box-shadow: ${theme.shadows.cardHover};
      }
    `}

  /* Clickable */
  ${({ $clickable }) =>
    $clickable &&
    css`
      cursor: pointer;
    `}

  /* Padding */
  ${({ $padding, theme }) => {
    if ($padding === false || $padding === 'none') return '';
    const paddingValue = theme.spacing[$padding] || theme.spacing.lg;
    return css`
      padding: ${paddingValue};
    `;
  }}
`;

const CardHeader = styled.div`
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: ${({ theme }) => theme.spacing.lg};
  border-bottom: 1px solid ${({ theme }) => theme.colors.borderLight};

  ${({ $noBorder }) =>
    $noBorder &&
    css`
      border-bottom: none;
    `}
`;

const CardTitle = styled.h3`
  font-size: ${({ theme }) => theme.typography.fontSizes.lg};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  color: ${({ theme }) => theme.colors.secondary};
  margin: 0;
`;

const CardSubtitle = styled.p`
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  color: ${({ theme }) => theme.colors.textMuted};
  margin: ${({ theme }) => theme.spacing.xs} 0 0;
`;

const CardContent = styled.div`
  padding: ${({ theme, $padding }) =>
    $padding === false ? 0 : theme.spacing[$padding] || theme.spacing.lg};
`;

const CardFooter = styled.div`
  display: flex;
  align-items: center;
  justify-content: ${({ $align }) => $align || 'flex-end'};
  gap: ${({ theme }) => theme.spacing.md};
  padding: ${({ theme }) => theme.spacing.lg};
  border-top: 1px solid ${({ theme }) => theme.colors.borderLight};
  background-color: ${({ theme }) => theme.colors.background};

  ${({ $noBorder }) =>
    $noBorder &&
    css`
      border-top: none;
      background-color: transparent;
    `}
`;

const CardImage = styled.div`
  position: relative;
  width: 100%;
  height: ${({ $height }) => $height || '200px'};
  overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
`;

const CardOverlay = styled.div`
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: ${({ theme }) => theme.spacing.lg};
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  color: white;
`;

const Card = ({
  children,
  variant = 'elevated',
  hoverable = false,
  clickable = false,
  padding = true,
  radius = 'xl',
  onClick,
  className,
  ...props
}) => {
  return (
    <StyledCard
      $variant={variant}
      $hoverable={hoverable}
      $clickable={clickable || !!onClick}
      $padding={padding}
      $radius={radius}
      onClick={onClick}
      className={className}
      {...props}
    >
      {children}
    </StyledCard>
  );
};

Card.Header = CardHeader;
Card.Title = CardTitle;
Card.Subtitle = CardSubtitle;
Card.Content = CardContent;
Card.Footer = CardFooter;
Card.Image = CardImage;
Card.Overlay = CardOverlay;

export default Card;

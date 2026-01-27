import React from 'react';
import styled, { css } from 'styled-components';

const StyledBadge = styled.span`
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: ${({ theme }) => theme.spacing.xs};
  font-family: ${({ theme }) => theme.typography.fontFamilyHebrew};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  white-space: nowrap;
  border-radius: ${({ theme, $pill }) =>
    $pill ? theme.borderRadius.full : theme.borderRadius.md};
  transition: all ${({ theme }) => theme.transitions.fast};

  /* Sizes */
  ${({ $size }) => {
    switch ($size) {
      case 'sm':
        return css`
          padding: 0.125rem 0.5rem;
          font-size: ${({ theme }) => theme.typography.fontSizes.xs};
        `;
      case 'lg':
        return css`
          padding: 0.5rem 1rem;
          font-size: ${({ theme }) => theme.typography.fontSizes.md};
        `;
      default: // md
        return css`
          padding: 0.25rem 0.75rem;
          font-size: ${({ theme }) => theme.typography.fontSizes.sm};
        `;
    }
  }}

  /* Variants */
  ${({ $variant, theme }) => {
    switch ($variant) {
      case 'success':
        return css`
          background-color: ${theme.colors.successLight};
          color: ${theme.colors.success};
        `;
      case 'warning':
        return css`
          background-color: ${theme.colors.warningLight};
          color: #b45309;
        `;
      case 'error':
        return css`
          background-color: ${theme.colors.errorLight};
          color: ${theme.colors.error};
        `;
      case 'info':
        return css`
          background-color: ${theme.colors.infoLight};
          color: ${theme.colors.info};
        `;
      case 'secondary':
        return css`
          background-color: ${theme.colors.background};
          color: ${theme.colors.textMuted};
        `;
      case 'outline':
        return css`
          background-color: transparent;
          border: 1px solid ${theme.colors.border};
          color: ${theme.colors.textMuted};
        `;
      default: // primary
        return css`
          background-color: ${theme.colors.primaryLight};
          color: ${theme.colors.primaryDark};
        `;
    }
  }}

  /* Dot indicator */
  ${({ $dot, $variant, theme }) =>
    $dot &&
    css`
      &::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: ${$variant === 'success'
          ? theme.colors.success
          : $variant === 'warning'
          ? '#b45309'
          : $variant === 'error'
          ? theme.colors.error
          : $variant === 'info'
          ? theme.colors.info
          : theme.colors.primary};
      }
    `}

  /* Icon */
  svg {
    width: ${({ $size }) => ($size === 'sm' ? '12px' : $size === 'lg' ? '18px' : '14px')};
    height: ${({ $size }) => ($size === 'sm' ? '12px' : $size === 'lg' ? '18px' : '14px')};
  }
`;

const CountBadge = styled.span`
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: ${({ $size }) => ($size === 'sm' ? '16px' : $size === 'lg' ? '28px' : '20px')};
  height: ${({ $size }) => ($size === 'sm' ? '16px' : $size === 'lg' ? '28px' : '20px')};
  padding: 0 ${({ theme }) => theme.spacing.xs};
  font-size: ${({ $size, theme }) =>
    $size === 'sm'
      ? theme.typography.fontSizes.xs
      : $size === 'lg'
      ? theme.typography.fontSizes.md
      : theme.typography.fontSizes.sm};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  border-radius: ${({ theme }) => theme.borderRadius.full};
  background-color: ${({ $variant, theme }) =>
    $variant === 'error' ? theme.colors.error : theme.colors.primary};
  color: white;
`;

const Badge = ({
  children,
  variant = 'primary',
  size = 'md',
  pill = true,
  dot = false,
  icon,
  className,
}) => {
  return (
    <StyledBadge
      $variant={variant}
      $size={size}
      $pill={pill}
      $dot={dot}
      className={className}
    >
      {icon}
      {children}
    </StyledBadge>
  );
};

Badge.Count = ({ count, max = 99, size = 'md', variant = 'primary' }) => {
  const displayCount = count > max ? `${max}+` : count;

  if (count <= 0) return null;

  return (
    <CountBadge $size={size} $variant={variant}>
      {displayCount}
    </CountBadge>
  );
};

export default Badge;

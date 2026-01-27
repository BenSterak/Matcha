import React from 'react';
import styled, { css, keyframes } from 'styled-components';

const spin = keyframes`
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
`;

const BaseButton = styled.button`
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: ${({ theme }) => theme.spacing.sm};
  font-family: ${({ theme }) => theme.typography.fontFamilyHebrew};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  cursor: pointer;
  transition: all ${({ theme }) => theme.transitions.fast};
  white-space: nowrap;
  text-decoration: none;
  border: none;
  outline: none;
  position: relative;

  /* Sizes */
  ${({ $size }) => {
    switch ($size) {
      case 'sm':
        return css`
          padding: 0.5rem 1rem;
          font-size: ${({ theme }) => theme.typography.fontSizes.sm};
          min-height: 36px;
        `;
      case 'lg':
        return css`
          padding: 1rem 2rem;
          font-size: ${({ theme }) => theme.typography.fontSizes.lg};
          min-height: 56px;
        `;
      default: // md
        return css`
          padding: 0.75rem 1.5rem;
          font-size: ${({ theme }) => theme.typography.fontSizes.md};
          min-height: 44px;
        `;
    }
  }}

  /* Full width */
  ${({ $fullWidth }) =>
    $fullWidth &&
    css`
      width: 100%;
    `}

  /* Variants */
  ${({ $variant, theme }) => {
    switch ($variant) {
      case 'secondary':
        return css`
          background-color: ${theme.colors.surface};
          color: ${theme.colors.secondary};
          border: 2px solid ${theme.colors.border};

          &:hover:not(:disabled) {
            background-color: ${theme.colors.surfaceHover};
            border-color: ${theme.colors.textLight};
          }

          &:active:not(:disabled) {
            transform: scale(0.98);
          }
        `;
      case 'outline':
        return css`
          background-color: transparent;
          color: ${theme.colors.primary};
          border: 2px solid ${theme.colors.primary};

          &:hover:not(:disabled) {
            background-color: ${theme.colors.primaryGlow};
          }

          &:active:not(:disabled) {
            transform: scale(0.98);
          }
        `;
      case 'ghost':
        return css`
          background-color: transparent;
          color: ${theme.colors.textMuted};

          &:hover:not(:disabled) {
            background-color: ${theme.colors.surfaceHover};
            color: ${theme.colors.text};
          }

          &:active:not(:disabled) {
            transform: scale(0.98);
          }
        `;
      case 'danger':
        return css`
          background-color: ${theme.colors.error};
          color: white;

          &:hover:not(:disabled) {
            background-color: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
          }

          &:active:not(:disabled) {
            transform: scale(0.98);
          }
        `;
      case 'success':
        return css`
          background-color: ${theme.colors.success};
          color: white;

          &:hover:not(:disabled) {
            background-color: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
          }

          &:active:not(:disabled) {
            transform: scale(0.98);
          }
        `;
      default: // primary
        return css`
          background-color: ${theme.colors.primary};
          color: white;
          box-shadow: 0 4px 12px ${theme.colors.primaryGlow};

          &:hover:not(:disabled) {
            background-color: ${theme.colors.primaryDark};
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
            transform: translateY(-1px);
          }

          &:active:not(:disabled) {
            transform: translateY(0) scale(0.98);
          }
        `;
    }
  }}

  /* Disabled state */
  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* Loading state */
  ${({ $loading }) =>
    $loading &&
    css`
      pointer-events: none;
      color: transparent;
    `}
`;

const Spinner = styled.span`
  position: absolute;
  width: 20px;
  height: 20px;
  border: 2px solid transparent;
  border-top-color: currentColor;
  border-radius: 50%;
  animation: ${spin} 0.6s linear infinite;
  color: ${({ $variant, theme }) =>
    $variant === 'primary' || $variant === 'danger' || $variant === 'success'
      ? 'white'
      : theme.colors.primary};
`;

const IconWrapper = styled.span`
  display: flex;
  align-items: center;
  justify-content: center;

  svg {
    width: ${({ $size }) => ($size === 'sm' ? '16px' : $size === 'lg' ? '24px' : '20px')};
    height: ${({ $size }) => ($size === 'sm' ? '16px' : $size === 'lg' ? '24px' : '20px')};
  }
`;

const Button = ({
  children,
  variant = 'primary',
  size = 'md',
  fullWidth = false,
  loading = false,
  disabled = false,
  icon = null,
  iconPosition = 'start',
  type = 'button',
  onClick,
  ...props
}) => {
  return (
    <BaseButton
      type={type}
      $variant={variant}
      $size={size}
      $fullWidth={fullWidth}
      $loading={loading}
      disabled={disabled || loading}
      onClick={onClick}
      {...props}
    >
      {loading && <Spinner $variant={variant} />}
      {icon && iconPosition === 'start' && (
        <IconWrapper $size={size}>{icon}</IconWrapper>
      )}
      {children}
      {icon && iconPosition === 'end' && (
        <IconWrapper $size={size}>{icon}</IconWrapper>
      )}
    </BaseButton>
  );
};

export default Button;

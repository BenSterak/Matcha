import React, { forwardRef, useState } from 'react';
import styled, { css } from 'styled-components';
import { Eye, EyeOff } from 'lucide-react';

const InputContainer = styled.div`
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.xs};
  width: 100%;
`;

const Label = styled.label`
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  font-weight: ${({ theme }) => theme.typography.fontWeights.medium};
  color: ${({ theme }) => theme.colors.secondary};
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.xs};
`;

const Required = styled.span`
  color: ${({ theme }) => theme.colors.error};
`;

const InputWrapper = styled.div`
  position: relative;
  display: flex;
  align-items: center;
`;

const StyledInput = styled.input`
  width: 100%;
  padding: ${({ theme }) => theme.spacing.md};
  padding-right: ${({ $hasIcon, theme }) => ($hasIcon ? theme.spacing.xxl : theme.spacing.md)};
  padding-left: ${({ $hasEndIcon, theme }) => ($hasEndIcon ? theme.spacing.xxl : theme.spacing.md)};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-family: ${({ theme }) => theme.typography.fontFamilyHebrew};
  color: ${({ theme }) => theme.colors.text};
  background-color: ${({ theme }) => theme.colors.surface};
  border: 2px solid ${({ theme }) => theme.colors.border};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  transition: all ${({ theme }) => theme.transitions.normal};
  direction: rtl;

  &::placeholder {
    color: ${({ theme }) => theme.colors.textLight};
  }

  &:hover:not(:disabled):not(:focus) {
    border-color: ${({ theme }) => theme.colors.textLight};
  }

  &:focus {
    outline: none;
    border-color: ${({ theme }) => theme.colors.primary};
    box-shadow: 0 0 0 3px ${({ theme }) => theme.colors.primaryGlow};
  }

  &:disabled {
    background-color: ${({ theme }) => theme.colors.background};
    cursor: not-allowed;
    opacity: 0.6;
  }

  ${({ $error, theme }) =>
    $error &&
    css`
      border-color: ${theme.colors.error};

      &:focus {
        border-color: ${theme.colors.error};
        box-shadow: 0 0 0 3px ${theme.colors.errorLight};
      }
    `}

  ${({ $success, theme }) =>
    $success &&
    css`
      border-color: ${theme.colors.success};

      &:focus {
        border-color: ${theme.colors.success};
        box-shadow: 0 0 0 3px ${theme.colors.successLight};
      }
    `}
`;

const StyledTextarea = styled.textarea`
  width: 100%;
  padding: ${({ theme }) => theme.spacing.md};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-family: ${({ theme }) => theme.typography.fontFamilyHebrew};
  color: ${({ theme }) => theme.colors.text};
  background-color: ${({ theme }) => theme.colors.surface};
  border: 2px solid ${({ theme }) => theme.colors.border};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  transition: all ${({ theme }) => theme.transitions.normal};
  direction: rtl;
  resize: vertical;
  min-height: ${({ $rows }) => ($rows ? `${$rows * 24}px` : '100px')};

  &::placeholder {
    color: ${({ theme }) => theme.colors.textLight};
  }

  &:hover:not(:disabled):not(:focus) {
    border-color: ${({ theme }) => theme.colors.textLight};
  }

  &:focus {
    outline: none;
    border-color: ${({ theme }) => theme.colors.primary};
    box-shadow: 0 0 0 3px ${({ theme }) => theme.colors.primaryGlow};
  }

  &:disabled {
    background-color: ${({ theme }) => theme.colors.background};
    cursor: not-allowed;
    opacity: 0.6;
  }

  ${({ $error, theme }) =>
    $error &&
    css`
      border-color: ${theme.colors.error};

      &:focus {
        border-color: ${theme.colors.error};
        box-shadow: 0 0 0 3px ${theme.colors.errorLight};
      }
    `}
`;

const IconContainer = styled.span`
  position: absolute;
  right: ${({ theme }) => theme.spacing.md};
  display: flex;
  align-items: center;
  justify-content: center;
  color: ${({ theme }) => theme.colors.textMuted};
  pointer-events: none;

  svg {
    width: 20px;
    height: 20px;
  }
`;

const EndIconContainer = styled.button`
  position: absolute;
  left: ${({ theme }) => theme.spacing.md};
  display: flex;
  align-items: center;
  justify-content: center;
  color: ${({ theme }) => theme.colors.textMuted};
  background: none;
  border: none;
  cursor: pointer;
  padding: ${({ theme }) => theme.spacing.xs};
  border-radius: ${({ theme }) => theme.borderRadius.sm};
  transition: color ${({ theme }) => theme.transitions.fast};

  &:hover {
    color: ${({ theme }) => theme.colors.text};
  }

  svg {
    width: 20px;
    height: 20px;
  }
`;

const HelperText = styled.span`
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  color: ${({ theme }) => theme.colors.textMuted};
`;

const ErrorText = styled.span`
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  color: ${({ theme }) => theme.colors.error};
`;

const CharCount = styled.span`
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  color: ${({ theme, $isOver }) => ($isOver ? theme.colors.error : theme.colors.textLight)};
  text-align: left;
  margin-top: ${({ theme }) => theme.spacing.xs};
`;

const Input = forwardRef(
  (
    {
      label,
      type = 'text',
      placeholder,
      value,
      onChange,
      onBlur,
      onFocus,
      error,
      helperText,
      icon,
      required = false,
      disabled = false,
      success = false,
      maxLength,
      rows = 4,
      name,
      id,
      autoComplete,
      ...props
    },
    ref
  ) => {
    const [showPassword, setShowPassword] = useState(false);
    const isTextarea = type === 'textarea';
    const isPassword = type === 'password';
    const inputType = isPassword && showPassword ? 'text' : type;
    const inputId = id || name;

    const currentLength = value?.length || 0;
    const isOverLimit = maxLength && currentLength > maxLength;

    return (
      <InputContainer>
        {label && (
          <Label htmlFor={inputId}>
            {icon && icon}
            {label}
            {required && <Required>*</Required>}
          </Label>
        )}

        {isTextarea ? (
          <>
            <StyledTextarea
              ref={ref}
              id={inputId}
              name={name}
              value={value}
              onChange={onChange}
              onBlur={onBlur}
              onFocus={onFocus}
              placeholder={placeholder}
              disabled={disabled}
              $error={!!error}
              $success={success}
              $rows={rows}
              maxLength={maxLength}
              {...props}
            />
            {maxLength && (
              <CharCount $isOver={isOverLimit}>
                {currentLength}/{maxLength}
              </CharCount>
            )}
          </>
        ) : (
          <InputWrapper>
            {icon && <IconContainer>{icon}</IconContainer>}
            <StyledInput
              ref={ref}
              id={inputId}
              name={name}
              type={inputType}
              value={value}
              onChange={onChange}
              onBlur={onBlur}
              onFocus={onFocus}
              placeholder={placeholder}
              disabled={disabled}
              autoComplete={autoComplete}
              $error={!!error}
              $success={success}
              $hasIcon={!!icon}
              $hasEndIcon={isPassword}
              {...props}
            />
            {isPassword && (
              <EndIconContainer
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                tabIndex={-1}
              >
                {showPassword ? <EyeOff /> : <Eye />}
              </EndIconContainer>
            )}
          </InputWrapper>
        )}

        {error && <ErrorText>{error}</ErrorText>}
        {helperText && !error && <HelperText>{helperText}</HelperText>}
      </InputContainer>
    );
  }
);

Input.displayName = 'Input';

export default Input;

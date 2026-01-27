import React, { forwardRef } from 'react';
import styled, { css } from 'styled-components';
import { ChevronDown } from 'lucide-react';

const SelectContainer = styled.div`
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

const SelectWrapper = styled.div`
  position: relative;
  display: flex;
  align-items: center;
`;

const StyledSelect = styled.select`
  width: 100%;
  padding: ${({ theme }) => theme.spacing.md};
  padding-left: ${({ theme }) => theme.spacing.xxl};
  font-size: ${({ theme }) => theme.typography.fontSizes.md};
  font-family: ${({ theme }) => theme.typography.fontFamilyHebrew};
  color: ${({ theme }) => theme.colors.text};
  background-color: ${({ theme }) => theme.colors.surface};
  border: 2px solid ${({ theme }) => theme.colors.border};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  transition: all ${({ theme }) => theme.transitions.normal};
  direction: rtl;
  appearance: none;
  cursor: pointer;

  &:hover:not(:disabled) {
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

  /* Placeholder styling */
  &:invalid,
  & option[value=''] {
    color: ${({ theme }) => theme.colors.textLight};
  }
`;

const ChevronIcon = styled.span`
  position: absolute;
  left: ${({ theme }) => theme.spacing.md};
  display: flex;
  align-items: center;
  justify-content: center;
  color: ${({ theme }) => theme.colors.textMuted};
  pointer-events: none;
  transition: transform ${({ theme }) => theme.transitions.fast};

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

const Select = forwardRef(
  (
    {
      label,
      options = [],
      placeholder = 'בחר אפשרות',
      value,
      onChange,
      onBlur,
      error,
      helperText,
      required = false,
      disabled = false,
      name,
      id,
      ...props
    },
    ref
  ) => {
    const selectId = id || name;

    return (
      <SelectContainer>
        {label && (
          <Label htmlFor={selectId}>
            {label}
            {required && <Required>*</Required>}
          </Label>
        )}

        <SelectWrapper>
          <StyledSelect
            ref={ref}
            id={selectId}
            name={name}
            value={value}
            onChange={onChange}
            onBlur={onBlur}
            disabled={disabled}
            $error={!!error}
            required={required}
            {...props}
          >
            {placeholder && (
              <option value="" disabled>
                {placeholder}
              </option>
            )}
            {options.map((option) => (
              <option
                key={option.value}
                value={option.value}
                disabled={option.disabled}
              >
                {option.label}
              </option>
            ))}
          </StyledSelect>
          <ChevronIcon>
            <ChevronDown />
          </ChevronIcon>
        </SelectWrapper>

        {error && <ErrorText>{error}</ErrorText>}
        {helperText && !error && <HelperText>{helperText}</HelperText>}
      </SelectContainer>
    );
  }
);

Select.displayName = 'Select';

export default Select;

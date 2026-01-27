import React from 'react';
import styled, { css } from 'styled-components';
import { User } from 'lucide-react';

const sizes = {
  xs: { size: '24px', fontSize: '10px', iconSize: 12 },
  sm: { size: '32px', fontSize: '12px', iconSize: 16 },
  md: { size: '48px', fontSize: '16px', iconSize: 24 },
  lg: { size: '64px', fontSize: '20px', iconSize: 32 },
  xl: { size: '96px', fontSize: '28px', iconSize: 48 },
  xxl: { size: '128px', fontSize: '36px', iconSize: 64 },
};

const AvatarContainer = styled.div`
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  border-radius: ${({ $square, theme }) => ($square ? theme.borderRadius.lg : '50%')};
  overflow: hidden;
  background-color: ${({ theme }) => theme.colors.primaryLight};
  color: ${({ theme }) => theme.colors.primary};
  font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};

  ${({ $size }) => {
    const s = sizes[$size] || sizes.md;
    return css`
      width: ${s.size};
      height: ${s.size};
      font-size: ${s.fontSize};
    `;
  }}

  ${({ $bordered, theme }) =>
    $bordered &&
    css`
      border: 3px solid ${theme.colors.surface};
      box-shadow: ${theme.shadows.sm};
    `}
`;

const AvatarImage = styled.img`
  width: 100%;
  height: 100%;
  object-fit: cover;
`;

const AvatarFallback = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  background-color: ${({ theme }) => theme.colors.primaryLight};
  color: ${({ theme }) => theme.colors.primary};
`;

const OnlineBadge = styled.span`
  position: absolute;
  bottom: ${({ $size }) => ($size === 'xs' || $size === 'sm' ? '0' : '2px')};
  right: ${({ $size }) => ($size === 'xs' || $size === 'sm' ? '0' : '2px')};
  width: ${({ $size }) =>
    $size === 'xs' ? '8px' : $size === 'sm' ? '10px' : $size === 'lg' || $size === 'xl' || $size === 'xxl' ? '16px' : '12px'};
  height: ${({ $size }) =>
    $size === 'xs' ? '8px' : $size === 'sm' ? '10px' : $size === 'lg' || $size === 'xl' || $size === 'xxl' ? '16px' : '12px'};
  background-color: ${({ $status, theme }) =>
    $status === 'online'
      ? theme.colors.success
      : $status === 'away'
      ? theme.colors.warning
      : theme.colors.textLight};
  border: 2px solid ${({ theme }) => theme.colors.surface};
  border-radius: 50%;
`;

const AvatarGroup = styled.div`
  display: flex;
  flex-direction: row-reverse;

  & > * {
    margin-left: -${({ $overlap }) => $overlap || '12px'};

    &:last-child {
      margin-left: 0;
    }
  }
`;

const Avatar = ({
  src,
  alt = '',
  name,
  size = 'md',
  square = false,
  bordered = false,
  status,
  onClick,
  className,
}) => {
  const getInitials = (name) => {
    if (!name) return '';
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return `${parts[0][0]}${parts[parts.length - 1][0]}`.toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
  };

  const iconSize = sizes[size]?.iconSize || sizes.md.iconSize;

  return (
    <AvatarContainer
      $size={size}
      $square={square}
      $bordered={bordered}
      onClick={onClick}
      className={className}
      style={onClick ? { cursor: 'pointer' } : undefined}
    >
      {src ? (
        <AvatarImage src={src} alt={alt || name} />
      ) : name ? (
        <AvatarFallback>{getInitials(name)}</AvatarFallback>
      ) : (
        <AvatarFallback>
          <User size={iconSize} />
        </AvatarFallback>
      )}
      {status && <OnlineBadge $status={status} $size={size} />}
    </AvatarContainer>
  );
};

Avatar.Group = AvatarGroup;

export default Avatar;

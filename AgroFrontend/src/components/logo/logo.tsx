import type { BoxProps } from '@mui/material/Box';
import { forwardRef } from 'react';

import Box from '@mui/material/Box';
import { useTheme } from '@mui/material/styles';

import { RouterLink } from 'src/routes/components';
import { logoClasses } from './classes';

export type LogoProps = BoxProps & {
  href?: string;
  isSingle?: boolean;
  disableLink?: boolean;
  logoFormat?: 'svg' | 'png'; // Option to switch between SVG and PNG formats
};

export const Logo = forwardRef<HTMLDivElement, LogoProps>(
  (
    {
      width,
      height,
      href = '/',
      isSingle = true,
      disableLink = false,
      className,
      sx,
      logoFormat = 'svg', // Default to SVG format
      ...other
    },
    ref
  ) => {
    const theme = useTheme();

    const singleLogoPath =
      logoFormat === 'svg'
        ? '/assets/logo/AgroLogo.svg'
        : '/assets/logo/AgroLogo.png'; // Dynamically switch logo path
    const fullLogoPath =
      logoFormat === 'svg'
        ? '/assets/logo/AgroLogo.svg'
        : '/assets/logo/AgroLogo.png';

    const singleLogo = (
      <Box
        alt="Single logo"
        component="img"
        src={singleLogoPath}
        width="100%"
        height="100%"
      />
    );

    const fullLogo = (
      <Box
        alt="Full logo"
        component="img"
        src={fullLogoPath}
        width="100%"
        height="100%"
      />
    );

    const baseSize = {
      width: width ?? 60,
      height: height ?? 50,
      ...(!isSingle && {
        width: width ?? 102,
        height: height ?? 36,
      }),
    };

    return (
      <Box
        ref={ref}
        component={disableLink ? 'div' : RouterLink}
        href={href}
        className={logoClasses.root.concat(className ? ` ${className}` : '')}
        aria-label="Logo"
        sx={{
          ...baseSize,
          flexShrink: 0,
          display: 'inline-flex',
          verticalAlign: 'middle',
          ...(disableLink && { pointerEvents: 'none' }),
          ...sx,
        }}
        {...other}
      >
        {isSingle ? singleLogo : fullLogo}
      </Box>
    );
  }
);

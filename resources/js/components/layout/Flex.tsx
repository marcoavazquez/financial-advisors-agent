import React from "react";

interface Props extends React.HTMLAttributes<HTMLDivElement> {
  gap?: string;
  direction?: 'row' | 'column';
  justifyContent?: 'flex-start' | 'flex-end' | 'center' | 'space-between' | 'space-around' | 'space-evenly';
  alignItems?: 'flex-start' | 'flex-end' | 'center' | 'baseline' | 'stretch';
  flex?: string | number;
  padding?: string;
}

export const Flex: React.FC<Props> = ({
  children,
  gap = '0px',
  direction = 'row',
  justifyContent = 'flex-start',
  alignItems = 'stretch',
  flex = 'none',
  padding,
  ...props
}) => {
  return (
    <div
      {...props}
      style={{
        gap: gap,
        flexDirection: direction,
        justifyContent: justifyContent,
        alignItems: alignItems,
        flex,
        padding,
      }}
      className={`flex ${props.className}`}
    >
      {children}
    </div>
  );
}
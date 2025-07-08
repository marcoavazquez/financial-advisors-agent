import { ButtonHTMLAttributes, ReactNode } from "react";

interface Props extends ButtonHTMLAttributes<HTMLButtonElement> {
  children: ReactNode
  variant?: 'primary' | 'secondary' | 'outlined'
}

export const Button: React.FC<Props> = ({ children, variant = 'primary',...props }) => {
  const classNames = `btn btn-${variant}`;
  return (
    <button {...props} className={classNames}>
      {children}
    </button>
  ); 
}
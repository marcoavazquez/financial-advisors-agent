interface Props {
  children: React.ReactNode;
  variant?: 'primary' | 'secondary' | 'danger' | 'success';
  onClick?: () => void;
}

export const IconButton: React.FC<Props> = ({ children, variant = 'primary', onClick }) => {
  const classNames = `btn-icon btn-${variant}`;
  return (
    <button className={classNames} onClick={onClick}>
      {children}
    </button>
  ); 
}

interface Props {
  children: React.ReactNode;
}

export const Container: React.FC<Props> = ({ children }) => {
  return (
    <div className="container">
      {children}
    </div>
  );
}

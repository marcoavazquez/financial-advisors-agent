import React from "react";

interface Props {
  children?: React.ReactNode;
  severity?: 'info' | 'warning' | 'danger' | 'success';
}
export const Alert: React.FC<Props> = ({ children, severity = 'info' }) => {
  return (
    <div className={`alert alert-${severity}`}>
      {children}
    </div>
  )
}
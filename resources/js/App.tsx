import React from "react";
import ChatPage from "./pages/Chat/ChatPage";
import { Container } from "./components";

const App: React.FC = () => {
  return (
    <main>
      <Container>
        <ChatPage />
      </Container>
    </main>
  )
}

export default App;
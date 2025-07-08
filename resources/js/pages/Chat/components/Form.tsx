import React, { useRef } from "react";
import { Button } from "../../../components";
import { AddIcon, MicrophoneIcon } from "../../../components/icons";
import { Flex } from "../../../components/layout";
import { Message } from "../../../types";

interface Props {
  formAction: (formData: FormData) => void;
}
const ChatForm: React.FC<Props> = ({
  formAction,
}) => {

  const formRef = useRef<HTMLFormElement>(null);

  const handleKeyDown = (event: React.KeyboardEvent<HTMLTextAreaElement>) => {
    const content = event.currentTarget.value;
    if (event.key === 'Enter' && !event.shiftKey && content?.trim() !== '') {
      formRef.current?.requestSubmit()
    }
  }

  return (
    <div className="chat-form">
      <form action={formAction} ref={formRef}>
        <textarea
          placeholder="Ask anything about your meetings..."
          name="content"
          onKeyDown={(e) => handleKeyDown(e)}
          rows={5}
        ></textarea>
      </form>
      <Flex gap="1rem" justifyContent="space-between">
        <Flex gap="0.5rem">
          <Button variant="outlined">
            <AddIcon />
          </Button>
          <Button variant="outlined">
            All meetings
          </Button>
          <Button variant="outlined">
            <img src="/images/integrations.png" alt="Integrations" />
          </Button>
          <Button variant="outlined">
            <img src="/images/emoneye.jpg" alt="Emoneye" />
          </Button>
        </Flex>
        <Button variant="outlined">
          <MicrophoneIcon />
        </Button>
      </Flex>
    </div>
  )
}

export default ChatForm;
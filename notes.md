- Import emails from gmail and records from hubspot (contacts and notes)

- Uses the previous data as context to answer the question, example:
  - Who mentioned theid kid plays baseball?
  - Why did greg say he wanted  to sell AAPL stock

- Implemente tool calling for asking the agent to do things for me
  - Store the tasks in databases with context for memorization, so that tasks that
    require waiting for a response can be continued until completion: Examples:
      - Schedule an appointment with Sara Smith:
        - this would look up Sara in Hubspot, or emails, email her asking to set up 
          an appointment (sharing available times from my calendar)
        - When she respond, take appropriate action:
          like add to calendar, make a note, of the interaction
          in Hubspot, respond letting them know its done.
        - IF they respond saying none of the times work, send some new times.
        - I should rely on the LLM and tool calling to handle edge cases.
          IT SHOULD BE EXTREMELY FLEXIBLE
      - I can give it ongoing instructions like:
        - When someone emails me that is not in Hubspot,
          please create a contact in Hubspot with a note about the email
          - Save the agent instructions and should consider those instructions when 
            webhooks from either gmail, calendar or Hubspot come in (or to use polling)
        - When I create a contact in Hubspot, send them an email telling then thank you being a client
        - When I add an event in my calendar, send an email to attendees tell them about the meeting
  - The Agent should be proactive:
    - Create hubspot webhook
    - Enable it on google too
    - Prompt it whenever something happens in the 3 integrations (gmail, calendar, hubspot) to see
      if it wants to proactively do something with any of the tools available
      - This should handle the case where a client emails me asking when our upcoming meeting
        is and the agent looks it up on the calendar and respond
  
- Check if the memory can be store in the database or if it can be implemented with the openai API

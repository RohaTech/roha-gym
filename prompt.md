## For Frontend

- use composable to write the logic and the ui in vue file
- do not use color hardcored instead use the color palette if not preset add using the tailwind 4 with this format `@theme {
  --color-mint-500: oklch(0.72 0.11 178);
}`
- use tanstack query for data fetching and state management
- use tanstack useform to handle forms
- use shadch vue components
- write the typescript type in separet file like loginType.ts
- use zod for validate the form and create a new file to put the zod schema like loginSchemas.ts
- do not put the text hardcorded instead use the below
  - for the language use the $lang from the main.ts for .vue files like login.vue
    use <p>$lang.login</p> but only in the template not in the script
  - for script and .ts file use languageStore.translations.login
    for example console.log(languageStore.translations.login.hello)
- always make the design mobile first design since the user mainly use mobile phones
  -I need you to implement the same error-handling pattern used in a composable and consumed by a form component.

Pattern to mirror (describe in your output and follow in code):

1. Composable maintains a reactive `errors` object (normalized field errors).
2. `clearError(field)` removes a field error when the user edits that field.
3. Mutations:
   - On validation errors from backend (`error.response.data.errors`), normalize them and set `errors`.
   - Otherwise show a toast with `error.response.data.message`.
4. Queries:
   - Catch failures, toast `error.response.data.message`, then rethrow.
5. Form submit:
   - Clear `errors`.
   - Validate with zod; if validation fails, set `errors` and stop.
   - On submit, use the mutation (create/update).
6. Form component:
   - Bind `:invalid` on each input based on `errors.field`.
   - Show `<small>` error message for each field.
   - Call `clearError('field')` on input/change to remove errors.

Deliverables:

- Provide a concise explanation of the pattern.
- Show the exact composable API: `errors`, `clearError`, `handleSubmit`, and mutation error handling.
- Show how the form template binds `:invalid`, renders the error text, and calls `clearError`.
- Keep the code style consistent with existing Vue SFC usage.

## For Backend

- put the translation used in front end in server\translations\Front\English.php
  and i will add the amharic manually by myself
- for backend response always use server\helper\Response\Response.php
- and for message use Message::get and add the message in server\translations\Message\English.php and i will add the amahric one by myself
- do not put number or value hardcored instead use server\appconfig

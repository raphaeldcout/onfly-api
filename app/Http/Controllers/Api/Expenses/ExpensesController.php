<?php

namespace App\Http\Controllers\Api\Expenses;

use App\DTOs\Expenses\ExpenseDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\{ExpensesDestroyRequest, ExpensesShowRequest, ExpensesStoreRequest, ExpensesUpdateRequest};
use App\Http\Resources\{ExpenseResource, ExpensesResource};
use App\Services\Expenses\ExpensesService;
use Symfony\Component\HttpFoundation\Response;

class ExpensesController extends Controller
{
    private ExpensesService $expensesService;

    public function __construct(ExpensesService $expensesService)
    {
        $this->expensesService = $expensesService;
    }

    public function index()
    {
        $expenses = $this->expensesService->getAll(auth()->user()->id);

        return (new ExpensesResource($expenses))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(ExpensesStoreRequest $request)
    {
        $expenseDTO = new ExpenseDTO(
            $request->input('description'),
            $request->input('date_registration'),
            $request->input('value')
        );

        $expense = $this->expensesService->create($expenseDTO, auth()->user()->id);

        return (new ExpenseResource($expense))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(ExpensesShowRequest $request, $id)
    {
        $expense = $this->expensesService->findById($id);

        if (!$expense) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_BAD_REQUEST);
        }

        if ($request->user()->cannot('view', [Expenses::class, $expense])) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_UNAUTHORIZED);
        }

        return (new ExpenseResource($expense))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(ExpensesUpdateRequest $request, $id)
    {
        $expense = $this->expensesService->findById($id);

        if (!$expense) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_BAD_REQUEST);
        }

        if ($request->user()->cannot('update', [Expenses::class, $expense])) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_UNAUTHORIZED);
        }

        $expenseDTO = new ExpenseDTO(
            $request->input('description'),
            $request->input('date_registration'),
            $request->input('value')
        );

        $expense = $this->expensesService->update($expenseDTO, $id, auth()->user()->id);

        return (new ExpenseResource($expense))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(ExpensesDestroyRequest $request, $id)
    {
        $expense = $this->expensesService->findById($id);

        if (!$expense) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_BAD_REQUEST);
        }

        if ($request->user()->cannot('delete', [Expenses::class, $expense])) {
            return response()->json(['message' => __('messages.expense_not_found')], Response::HTTP_UNAUTHORIZED);
        }

        $this->expensesService->delete($id, auth()->user()->id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}

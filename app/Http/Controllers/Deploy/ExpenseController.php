<?php

namespace App\Http\Controllers\Deploy;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Deploy\Expense;
use App\Plan\Topic;
use Auth;

class ExpenseController extends Controller
{
    private $finish = array(4, 5, 6);
    private $expense;

    public function __construct()
    {
        $this->expense = new Expense();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($expense = null)
    {
        $data = array(
            'topic' => Topic::whereIn('pk_imadt', function ($query) {
                $query->select('fk_imadt')->from('tblhopdong');
            })->whereNotIn('pk_imadt', $this->finish)->get(),
            'expense_list' => $this->expense->getExpense(),
            // 'expense' => $expense['expense'],
            // 'other' => $expense['other']
        );
        return view('deploy.expense.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMoneyText(Request $request)
    {
        if ($request->ajax())
        {
            $money = str_replace('.', '', $request->money);
            $money = mb_ucfirst($money);
            return response()->json($money);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->expense->pk_imadt = $request->TxtDetai;
        $this->expense->inam     = $request->TxtNgaycap;
        $this->expense->fkinhphi = $request->TxtKinhphi;
        $this->expense->sghichu  = $request->TxtGhichu;
        $this->expense->sotienchu = $request->txtSotien;
        $this->expense->fk_smacanbo = Auth::user()->pk_smacanbo;
        $this->expense->save();
        return redirect()->route('admin.expense.index')->with([
            'flash_level' => 'success',
            'flash_message' => 'C???p nh???t ph??n b??? kinh ph?? th??nh c??ng'
        ]);
    }

    /**
     * View common
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = array(
            'expense' => Expense::findOrFail($id),
            'other'   => Expense::where('pk_imadt', Expense::findOrFail($id)->pk_imadt)
                                ->get()
                                ->toArray()
        );

        return $this->index($data);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->expense = Expense::findOrFail($id);
        $this->expense->pk_imadt = $request->TxtDetai;
        $this->expense->inam     = $request->TxtNgaycap;
        $this->expense->fkinhphi = $request->TxtKinhphi;
        $this->expense->sghichu  = $request->TxtGhichu;
        $this->expense->sotienchu = $request->txtSotien;
        $this->expense->fk_smacanbo = Auth::user()->pk_smacanbo;
        $this->expense->save();
        return redirect()->route('admin.expense.index')->with([
            'flash_level' => 'success',
            'flash_message' => 'C???p nh???t ph??n b??? kinh ph?? th??nh c??ng'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->expense = Expense::findOrFail($id);
        $this->expense->delete($id);
        return redirect()->route('admin.expense.index')->with([
            'flash_level' => 'success',
            'flash_message' => 'X??a ph??n b??? kinh ph?? th??nh c??ng'
        ]);
    }


    public function getExpenseTopic(Request $request)
    {
        if ($request->ajax())
        {
            $result = Expense::where('pk_imadt', $request->topicID)
                                ->get();
            if (count ($result))
            {
                $content = "";
                foreach ($result AS $k => $r)
                {
                $content .= '<a class="btn btn-primary btn-block" style="margin-bottom: 2px;" data-toggle="modal" href="#modal-id'.$k.'">'.$r->inam.'</a>';
                    $content .= '<div class="modal fade" id="modal-id'.$k.'">';
                            $content .= '<div class="modal-dialog">';
                                $content .= '<div class="modal-content">';
                                    $content .= '<div class="modal-header">';
                                        $content .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
                                        $content .= '<h4 class="modal-title">L???n c???p kinh ph?? ng??y: '.$r->inam.'</h4>';
                                    $content .= '</div>';

                                    $content .= '<div class="modal-body">';
                                        $content .= '<table class="table table-bordered table-stripped">';
                                            $content .= '<tr>';
                                                $content .= '<th>Kinh ph??</th>';
                                                $content .= '<td>'.$r->fkinhphi.' ('.$r->sotienchu.') </td>';
                                            $content .= '</tr>';

                                            $content .= '<tr>';
                                                $content .= '<th>Ghi ch??</th>';
                                                $content .= '<td>'.$r->sghichu.'</td>';

                                            $content .= '</tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">????ng</button>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
            }
            else {
                $content = "Kh??ng c?? d??? li???u";
            }

            return response()->json($content);
        }
    }
}
